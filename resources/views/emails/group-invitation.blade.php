@php
    /** @var \App\Models\Group $group */
    /** @var \App\Models\GroupMember $member */
    /** @var string $inviterName */
    /** @var \Illuminate\Support\Carbon $expiresAt */
@endphp

<x-mail::message>
# {{ $inviterName }} invited you to join a group

You’ve been invited to join the group **{{ $group->name }}**.

<x-mail::panel>
**Group:** {{ $group->name }}  
**Invited by:** {{ $inviterName }}  
**Expires:** {{ $expiresAt->toDayDateTimeString() }} (in 7 days)
</x-mail::panel>

Click the button below to accept this invitation and join the group.

<x-mail::button :url="$url">
Join {{ $group->name }}
</x-mail::button>

If the button doesn’t work, copy and paste this link into your browser:

{{ $url }}

If this invite has expired or you weren’t expecting it, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
