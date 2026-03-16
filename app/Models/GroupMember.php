<?php

namespace App\Models;

use App\Enums\GroupMemberStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'group_id',
        'user_id',
        'invite_email',
        'invite_token',
        'status',
        'joined_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => GroupMemberStatus::class,
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
