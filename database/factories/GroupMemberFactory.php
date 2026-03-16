<?php

namespace Database\Factories;

use App\Models\GroupMember;
use App\Models\Group;
use App\Models\User;
use App\Enums\GroupMemberStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GroupMember>
 */
class GroupMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'user_id' => User::factory(),
            'invite_email' => null,
            'invite_token' => null,
            'status' => GroupMemberStatus::Accepted,
            'joined_at' => now(),
        ];
    }
}
