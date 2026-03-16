<?php

namespace App\Policies;

use App\Enums\GroupMemberStatus;
use App\Models\Expense;
use App\Models\Group;
use App\Models\User;

class ExpensePolicy
{
    public function create(User $user, Group $group): bool
    {
        return $group->members()
            ->where('user_id', $user->id)
            ->where('status', GroupMemberStatus::Accepted->value)
            ->exists();
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $expense->paid_by === $user->id;
    }
}

