<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\GroupMemberStatus;
use App\Mail\GroupInvitation;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

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
