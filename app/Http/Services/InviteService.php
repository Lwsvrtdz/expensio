<?php

namespace App\Http\Services;

use App\Enums\GroupMemberStatus;
use App\Models\ExpenseSplit;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InviteService
{
    public function acceptForUser(User $user, string $token): GroupMember
    {
        /** @var GroupMember $member */
        $member = GroupMember::query()
            ->where('invite_token', $token)
            ->where('status', GroupMemberStatus::Pending->value)
            ->first();

        if (! $member) {
            throw new ModelNotFoundException();
        }

        $oldEmail = $member->invite_email;

        $member->fill([
            'user_id' => $user->id,
            'invite_email' => null,
            'invite_token' => null,
            'status' => GroupMemberStatus::Accepted->value,
            'joined_at' => now(),
        ])->save();

        if ($oldEmail !== null) {
            ExpenseSplit::query()
                ->whereNull('user_id')
                ->where('member_email', $oldEmail)
                ->update([
                    'user_id' => $user->id,
                ]);
        }

        return $member;
    }
}

