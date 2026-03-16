@php
    /** @var \App\Models\Group $group */
    /** @var \App\Models\GroupMember $member */
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Group Invitation</title>
</head>
<body>
    <h1>You're invited to join {{ $group->name }}</h1>

    <p>
        Click the button below to accept this invitation and join the group.
    </p>

    <form method="POST" action="{{ route('invite.accept.store', ['token' => $token]) }}">
        @csrf
        <button type="submit">
            Accept Invitation
        </button>
    </form>
</body>
</html>

