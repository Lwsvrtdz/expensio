<?php

namespace App\Mail;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class GroupInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public GroupMember $member,
        public Group $group,
        public string $inviterName,
    ) {
    }

    public function build(): self
    {
        $expiresAt = now()->addDays(7);

        $url = URL::temporarySignedRoute('invite.accept', $expiresAt, [
            'token' => $this->member->invite_token,
        ]);

        return $this->subject("You're invited to join {$this->group->name}")
            ->markdown('emails.group-invitation', [
                'group' => $this->group,
                'member' => $this->member,
                'url' => $url,
                'inviterName' => $this->inviterName,
                'expiresAt' => $expiresAt,
            ]);
    }
}
