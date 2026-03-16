<?php

declare(strict_types=1);

use App\Enums\GroupMemberStatus;
use App\Models\Expense;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('splits a group expense equally between accepted members when no splits are provided', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $user->id]);

    GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => $user->id,
        'status' => GroupMemberStatus::Accepted,
    ]);

    GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => $other->id,
        'status' => GroupMemberStatus::Accepted,
    ]);

    $this->actingAs($user)
        ->post(route('groups.expenses.store', $group), [
            'description' => 'Dinner',
            'amount' => 10.00,
        ])
        ->assertRedirect();

    /** @var Expense $expense */
    $expense = Expense::query()->firstOrFail();

    expect($expense->splits)->toHaveCount(2);

    $total = $expense->splits->sum('amount');

    expect($total)->toBe(10.00);
});

it('only allows the payer to delete an expense', function () {
    $payer = User::factory()->create();
    $other = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $payer->id]);

    GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => $payer->id,
        'status' => GroupMemberStatus::Accepted,
    ]);

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

