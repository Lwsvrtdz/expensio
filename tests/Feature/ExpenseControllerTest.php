<?php

declare(strict_types=1);

use App\Enums\GroupMemberStatus;
use App\Models\Expense;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function addAcceptedMember(Group $group, User $user): GroupMember
{
    return GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => $user->id,
        'invite_email' => null,
        'invite_token' => null,
        'status' => GroupMemberStatus::Accepted,
    ]);
}

function addPendingMember(Group $group, string $email): GroupMember
{
    return GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => null,
        'invite_email' => $email,
        'status' => GroupMemberStatus::Pending,
        'joined_at' => null,
    ]);
}

it('stores a group expense with a valid accepted user payer', function () {
    $actor = User::factory()->create();
    $payer = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $actor->id]);

    addAcceptedMember($group, $actor);
    addAcceptedMember($group, $payer);

    $this->actingAs($actor)
        ->post(route('groups.expenses.store', $group), [
            'description' => 'Dinner',
            'amount' => 12.40,
            'payer_key' => 'user:' . $payer->id,
        ])
        ->assertRedirect();

    /** @var Expense $expense */
    $expense = Expense::query()->firstOrFail();

    expect($expense->paid_by)->toBe($payer->id)
        ->and($expense->payer_email)->toBeNull()
        ->and($expense->description)->toBe('Dinner')
        ->and($expense->amount)->toBe(12.4);
});

it('stores a group expense with an invited email payer', function () {
    $actor = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $actor->id]);

    addAcceptedMember($group, $actor);
    addPendingMember($group, 'friend@example.com');

    $this->actingAs($actor)
        ->post(route('groups.expenses.store', $group), [
            'description' => 'Boat rental',
            'amount' => 42.75,
            'payer_key' => 'email:friend@example.com',
        ])
        ->assertRedirect();

    /** @var Expense $expense */
    $expense = Expense::query()->firstOrFail();

    expect($expense->paid_by)->toBeNull()
        ->and($expense->payer_email)->toBe('friend@example.com');
});

it('defaults personal expenses to the authenticated actor as payer', function () {
    $actor = User::factory()->create();

    $this->actingAs($actor)
        ->post(route('expenses.store'), [
            'description' => 'Coffee',
            'amount' => 4.25,
        ])
        ->assertRedirect();

    /** @var Expense $expense */
    $expense = Expense::query()->with('splits')->firstOrFail();

    expect($expense->group_id)->toBeNull()
        ->and($expense->paid_by)->toBe($actor->id)
        ->and($expense->payer_email)->toBeNull()
        ->and($expense->splits)->toHaveCount(1)
        ->and((float) $expense->splits->first()->amount)->toBe(4.25);
});

it('splits a group expense equally between all members when no manual splits are provided', function () {
    $actor = User::factory()->create();
    $other = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $actor->id]);

    addAcceptedMember($group, $actor);
    addAcceptedMember($group, $other);
    addPendingMember($group, 'pending@example.com');

    $this->actingAs($actor)
        ->post(route('groups.expenses.store', $group), [
            'description' => 'Dinner',
            'amount' => 10.00,
            'payer_key' => 'user:' . $actor->id,
        ])
        ->assertRedirect();

    /** @var Expense $expense */
    $expense = Expense::query()->with('splits')->firstOrFail();

    expect($expense->splits)->toHaveCount(3)
        ->and((float) $expense->splits->sum('amount'))->toBe(10.0);

    $this->assertDatabaseHas('expense_splits', [
        'expense_id' => $expense->id,
        'user_id' => null,
        'member_email' => 'pending@example.com',
    ]);
});

it('stores manual splits exactly as submitted', function () {
    $actor = User::factory()->create();
    $other = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $actor->id]);

    addAcceptedMember($group, $actor);
    addAcceptedMember($group, $other);
    addPendingMember($group, 'pending@example.com');

    $this->actingAs($actor)
        ->post(route('groups.expenses.store', $group), [
            'description' => 'Tickets',
            'amount' => 10.00,
            'payer_key' => 'user:' . $actor->id,
            'splits' => [
                [
                    'user_id' => $other->id,
                    'member_email' => null,
                    'amount' => 6.25,
                ],
                [
                    'user_id' => null,
                    'member_email' => 'pending@example.com',
                    'amount' => 3.75,
                ],
            ],
        ])
        ->assertRedirect();

    /** @var Expense $expense */
    $expense = Expense::query()->with('splits')->firstOrFail();

    expect($expense->splits)->toHaveCount(2);

    $this->assertDatabaseHas('expense_splits', [
        'expense_id' => $expense->id,
        'user_id' => $other->id,
        'member_email' => null,
        'amount' => 6.25,
    ]);

    $this->assertDatabaseHas('expense_splits', [
        'expense_id' => $expense->id,
        'user_id' => null,
        'member_email' => 'pending@example.com',
        'amount' => 3.75,
    ]);
});

it('stores manual splits when sent with memberKey format', function () {
    $actor = User::factory()->create();
    $other = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $actor->id]);

    addAcceptedMember($group, $actor);
    addAcceptedMember($group, $other);
    addPendingMember($group, 'grey@test.com');
    addPendingMember($group, 'Louisevirtudazo@gmail.com');

    $this->actingAs($actor)
        ->post(route('groups.expenses.store', $group), [
            'description' => 'asd',
            'amount' => 40,
            'payerKey' => 'user:' . $actor->id,
            'splits' => [
                ['memberKey' => 'user:' . $actor->id, 'amount' => 10],
                ['memberKey' => 'email:grey@test.com', 'amount' => 10],
                ['memberKey' => 'user:' . $other->id, 'amount' => 10],
                ['memberKey' => 'email:Louisevirtudazo@gmail.com', 'amount' => 10],
            ],
        ])
        ->assertRedirect();

    /** @var Expense $expense */
    $expense = Expense::query()->with('splits')->firstOrFail();

    expect($expense->splits)->toHaveCount(4);

    $this->assertDatabaseHas('expense_splits', [
        'expense_id' => $expense->id,
        'user_id' => $actor->id,
        'member_email' => null,
        'amount' => 10,
    ]);
    $this->assertDatabaseHas('expense_splits', [
        'expense_id' => $expense->id,
        'user_id' => $other->id,
        'member_email' => null,
        'amount' => 10,
    ]);
    $this->assertDatabaseHas('expense_splits', [
        'expense_id' => $expense->id,
        'user_id' => null,
        'member_email' => 'grey@test.com',
        'amount' => 10,
    ]);
    $this->assertDatabaseHas('expense_splits', [
        'expense_id' => $expense->id,
        'user_id' => null,
        'member_email' => 'Louisevirtudazo@gmail.com',
        'amount' => 10,
    ]);
});

it('rejects an invalid payer key for a group expense', function () {
    $actor = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $actor->id]);

    addAcceptedMember($group, $actor);

    $this->actingAs($actor)
        ->from(route('groups.show', $group))
        ->post(route('groups.expenses.store', $group), [
            'description' => 'Transport',
            'amount' => 8.00,
            'payer_key' => 'user:999999',
        ])
        ->assertSessionHasErrors('payer_key');

    $this->assertDatabaseCount('expenses', 0);
});

it('rejects a group expense when the payer is missing', function () {
    $actor = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $actor->id]);

    addAcceptedMember($group, $actor);

    $this->actingAs($actor)
        ->from(route('groups.show', $group))
        ->post(route('groups.expenses.store', $group), [
            'description' => 'Lunch',
            'amount' => 15.00,
        ])
        ->assertSessionHasErrors('payer_key');

    $this->assertDatabaseCount('expenses', 0);
});

it('accepts payerKey as a fallback input name', function () {
    $actor = User::factory()->create();
    $payer = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $actor->id]);

    addAcceptedMember($group, $actor);
    addAcceptedMember($group, $payer);

    $this->actingAs($actor)
        ->post(route('groups.expenses.store', $group), [
            'description' => 'Museum',
            'amount' => 18.00,
            'payerKey' => 'user:' . $payer->id,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('expenses', [
        'group_id' => $group->id,
        'paid_by' => $payer->id,
        'payer_email' => null,
        'description' => 'Museum',
        'amount' => 18.00,
    ]);
});

it('only allows the payer to delete an expense', function () {
    $payer = User::factory()->create();
    $other = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $payer->id]);

    addAcceptedMember($group, $payer);

    /** @var Expense $expense */
    $expense = Expense::factory()->create([
        'group_id' => $group->id,
        'paid_by' => $payer->id,
    ]);

    $this->actingAs($other)
        ->delete(route('expenses.destroy', $expense))
        ->assertForbidden();

    $this->actingAs($payer)
        ->delete(route('expenses.destroy', $expense))
        ->assertRedirect();

    $this->assertDatabaseMissing('expenses', [
        'id' => $expense->id,
    ]);
});
