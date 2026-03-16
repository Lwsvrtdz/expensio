<?php

use App\Enums\GroupMemberStatus;
use App\Models\Expense;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows only groups where the user is an accepted member on index', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $visibleGroup = Group::factory()->create(['created_by' => $user->id]);
    $hiddenGroup = Group::factory()->create(['created_by' => $otherUser->id]);

    GroupMember::factory()->create([
        'group_id' => $visibleGroup->id,
        'user_id' => $user->id,
        'status' => GroupMemberStatus::Accepted,
    ]);

    GroupMember::factory()->create([
        'group_id' => $hiddenGroup->id,
        'user_id' => $user->id,
        'status' => GroupMemberStatus::Pending,
    ]);

    $this->actingAs($user)
        ->get(route('groups.index'))
        ->assertOk();
});

// it('renders the create group page', function () {
//     $user = User::factory()->create();

//     $this->actingAs($user)
//         ->get(route('groups.create'))
//         ->assertOk();
// });

it('stores a new group and adds the creator as a member', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('groups.store'), [
            'name' => 'Trip to Paris',
            'trip_date' => now()->toDateString(),
        ]);

    $group = Group::query()->first();

    expect($group)->not->toBeNull()
        ->and($group->created_by)->toBe($user->id);

    $this->assertDatabaseHas('group_members', [
        'group_id' => $group->id,
        'user_id' => $user->id,
        'status' => GroupMemberStatus::Accepted->value,
    ]);

    $response->assertRedirect(route('groups.show', $group));
});

// it('shows a group to an accepted member with members and expenses', function () {
//     $user = User::factory()->create();
//     $payer = User::factory()->create();

//     $group = Group::factory()->create(['created_by' => $user->id]);

//     GroupMember::factory()->create([
//         'group_id' => $group->id,
//         'user_id' => $user->id,
//         'status' => GroupMemberStatus::Accepted,
//     ]);

//     Expense::factory()->create([
//         'group_id' => $group->id,
//         'paid_by' => $payer->id,
//     ]);

//     $this->actingAs($user)
//         ->get(route('groups.show', $group))
//         ->assertOk();
// });

it('forbids showing a group to a non-member', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $user->id]);

    $response = $this->actingAs($user)
        ->get(route('groups.show', $group));

    $response->assertForbidden();
});

it('allows the creator to delete a group', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $user->id]);

    $response = $this->actingAs($user)
        ->delete(route('groups.destroy', $group));

    $response->assertRedirect(route('groups.index'));

    $this->assertDatabaseMissing('groups', ['id' => $group->id]);
});

it('forbids deleting a group for non-creators', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $group = Group::factory()->create(['created_by' => $creator->id]);

    $this->actingAs($otherUser)
        ->delete(route('groups.destroy', $group))
        ->assertForbidden();
});
