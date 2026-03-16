<?php

namespace App\Http\Services;

use App\Enums\GroupMemberStatus;
use App\Http\Requests\InviteMemberRequest;
use App\Mail\GroupInvitation;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GroupMemberService
{
    public function store(User $actor, Group $group, InviteMemberRequest $request): GroupMember
    {
        $isMember = $group->members()
            ->where('user_id', $actor->id)
            ->where('status', GroupMemberStatus::Accepted->value)
            ->exists();

        abort_unless($isMember, 403);

        $email = $request->string('email')->toString();

        $existingUser = User::query()
            ->where('email', $email)
            ->first();

        if ($existingUser) {
            return GroupMember::query()->create([
                'group_id' => $group->id,
                'user_id' => $existingUser->id,
                'invite_email' => null,
                'invite_token' => null,
                'status' => GroupMemberStatus::Accepted->value,
                'joined_at' => now(),
            ]);
        }

        $member = GroupMember::query()->create([
            'group_id' => $group->id,
            'user_id' => null,
            'invite_email' => $email,
            'invite_token' => Str::random(32),
            'status' => GroupMemberStatus::Pending->value,
            'joined_at' => null,
        ]);

        Mail::to($email)->send(new GroupInvitation($member, $group));

        return $member;
    }
}

