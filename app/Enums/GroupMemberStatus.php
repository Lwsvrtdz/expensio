<?php

namespace App\Enums;

enum GroupMemberStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
}

