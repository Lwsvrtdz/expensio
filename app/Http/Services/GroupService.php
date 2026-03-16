<?php

namespace App\Http\Services;

use App\Enums\GroupMemberStatus;
use App\Http\Requests\StoreGroupRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Support\Collection;

class GroupService
{
    public function index(User $actor): Collection
    {
        return Group::query()
            ->whereHas('members', function ($query) use ($actor) {
                $query->where('user_id', $actor->id)
                    ->where('status', GroupMemberStatus::Accepted->value);
            })
            ->withCount('expenses as latest_expense_count')
            ->get();
    }

    public function store(User $actor, StoreGroupRequest $request): Group
    {
        $group = Group::query()->create([
            'name' => $request->string('name')->toString(),
            'trip_date' => $request->date('trip_date'),
            'created_by' => $actor->id,
        ]);

        GroupMember::query()->create([
            'group_id' => $group->id,
            'user_id' => $actor->id,
            'invite_email' => null,
            'invite_token' => null,
            'status' => GroupMemberStatus::Accepted->value,
            'joined_at' => now(),
        ]);

        return $group;
    }

    public function show(User $actor, Group $group): Group
    {
        $isMember = $group->members()
            ->where('user_id', $actor->id)
            ->where('status', GroupMemberStatus::Accepted->value)
            ->exists();

        abort_unless($isMember, 403);

        return $group->load([
            'members.user',
            'expenses.payer',
        ]);
    }

    public function destroy(User $actor, Group $group): void
    {
        abort_unless($group->created_by === $actor->id, 403);

        $group->delete();
    }
}

