@php
    /** @var \App\Models\Group $group */
    /** @var \App\Models\GroupMember $member */
@endphp

<x-mail::message>
# You have been invited to join a group

You have been invited to join the group **{{ $group->name }}**.

@if ($member->relationLoaded('user') && $member->user)
Invited by: {{ $member->user->name }}
@endif

<x-mail::button :url="$url">
Accept Invitation
</x-mail::button>

If the button does not work, copy and paste this link into your browser:

{{ $url }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
