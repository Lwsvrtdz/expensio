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
    ) {
    }

    public function build(): self
    {
        $url = URL::signedRoute('invite.accept', ['token' => $this->member->invite_token]);

        return $this->subject('You have been invited to join a group')
            ->markdown('emails.group-invitation', [
                'group' => $this->group,
                'member' => $this->member,
                'url' => $url,
            ]);
    }
}
