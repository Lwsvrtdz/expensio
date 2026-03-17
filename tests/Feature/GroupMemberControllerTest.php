<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\GroupMemberStatus;
use App\Mail\GroupInvitation;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);


beforeEach(function () {
    Mail::fake();
});

it('allows an accepted member to add an existing user as accepted member', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $user->id]);


    GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => $user->id,
        'status' => GroupMemberStatus::Accepted,
    ]);

    $this->assertDatabaseHas('group_members', [
        'group_id' => $group->id,
        'user_id' => $user->id,
        'status' => GroupMemberStatus::Accepted->value,
    ]);

    $email = 'invitee@example.com';

    $response = $this->actingAs($user)
        ->post(route('groups.members.store', $group), [
            'email' => $email,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $member = GroupMember::query()
        ->where('group_id', $group->id)
        ->where('invite_email', $email)
        ->first();

    expect($member)->not->toBeNull()
        ->and($member->status)->toBe(GroupMemberStatus::Pending)
        ->and($member->invite_token)->not->toBeNull();

    Mail::assertSent(GroupInvitation::class, function (GroupInvitation $mail) use ($email) {
        return $mail->hasTo($email);
    });
});

it('forbids non-members from inviting', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $user->id]);

    $other = User::factory()->create();

    $this->actingAs($other)
        ->post(route('groups.members.store', $group), [
            'email' => 'someone@example.com',
        ])
        ->assertForbidden();
});

it('does not allow inviting the same email twice for a group', function () {
    Mail::fake();

    $user = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $user->id]);

    GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => null,
        'invite_email' => 'duplicate@example.com',
        'status' => GroupMemberStatus::Pending,
    ]);

    GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => $user->id,
        'status' => GroupMemberStatus::Accepted,
    ]);

    $response = $this->actingAs($user)
        ->from(route('groups.show', $group))
        ->post(route('groups.members.store', $group), [
            'email' => 'duplicate@example.com',
        ]);

    $response->assertSessionHasErrors('email');
});

it('allows a logged-in user to accept an invite and link expense splits', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $user->id]);

    $pending = GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => null,
        'invite_email' => 'invited@example.com',
        'status' => GroupMemberStatus::Pending,
    ]);

    $expenseSplit = ExpenseSplit::factory()->create([
        'user_id' => null,
        'member_email' => 'invited@example.com',
    ]);

    $url = URL::signedRoute('invite.accept', ['token' => $pending->invite_token]);

    $this->actingAs($user)
        ->get($url)
        ->assertOk();

    $this->actingAs($user)
        ->post(route('invite.accept.store', ['token' => $pending->invite_token]))
        ->assertRedirect(route('groups.show', $group->id));

    $this->assertDatabaseHas('group_members', [
        'group_id' => $group->id,
        'user_id' => $user->id,
        'status' => GroupMemberStatus::Accepted->value,
        'invite_email' => null,
    ]);

    $this->assertDatabaseHas('expense_splits', [
        'member_email' => 'invited@example.com',
        'user_id' => $user->id,
    ]);
});

it('upgrades expenses with payer_email to paid_by when invite is accepted', function () {
    $creator = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $creator->id]);

    $pending = GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => null,
        'invite_email' => 'payer@example.com',
        'status' => GroupMemberStatus::Pending,
    ]);

    $expense = Expense::factory()->create([
        'group_id' => $group->id,
        'paid_by' => null,
        'payer_email' => 'payer@example.com',
    ]);

    $user = User::factory()->create(['email' => 'payer@example.com']);

    $this->actingAs($user)
        ->post(route('invite.accept.store', ['token' => $pending->invite_token]))
        ->assertRedirect(route('groups.show', $group->id));

    $expense->refresh();

    expect($expense->paid_by)->toBe($user->id)
        ->and($expense->payer_email)->toBeNull();

    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
        'paid_by' => $user->id,
        'payer_email' => null,
    ]);
});

it('stores pending invite token in session when unauthenticated user opens invite', function () {
    $group = Group::factory()->create(['created_by' => User::factory()->create()->id]);

    $pending = GroupMember::factory()->create([
        'group_id' => $group->id,
        'user_id' => null,
        'invite_email' => 'someone@example.com',
        'status' => GroupMemberStatus::Pending,
    ]);

    $url = URL::signedRoute('invite.accept', ['token' => $pending->invite_token]);

    $response = $this->get($url);

    $response->assertRedirect(route('register'));
    $this->assertSame($pending->invite_token, session('pending_invite_token'));
});
