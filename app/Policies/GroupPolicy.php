<?php

namespace App\Policies;

use App\Enums\GroupMemberStatus;
use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function view(User $user, Group $group): bool
    {
        return $group->members()
            ->where('user_id', $user->id)
            ->where('status', GroupMemberStatus::Accepted->value)
            ->exists();
    }

    public function delete(User $user, Group $group): bool
    {
        return $group->created_by === $user->id;
    }

    public function invite(User $user, Group $group): bool
    {
        return $this->view($user, $group);
    }
}

