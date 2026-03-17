<?php

namespace App\Http\Services;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\User;

class UserParticipantLinkService
{
    public function linkForUserEmail(User $user, string $email, ?string $groupId = null): void
    {
        ExpenseSplit::query()
            ->whereNull('user_id')
            ->where('member_email', $email)
            ->when($groupId !== null, function ($query) use ($groupId) {
                $query->whereHas('expense', function ($subQuery) use ($groupId) {
                    $subQuery->where('group_id', $groupId);
                });
            })
            ->update([
                'user_id' => $user->id,
            ]);

        Expense::query()
            ->whereNull('paid_by')
            ->where('payer_email', $email)
            ->when($groupId !== null, function ($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->update([
                'paid_by' => $user->id,
                'payer_email' => null,
            ]);
    }
}
