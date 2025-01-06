<?php

namespace App\Enums;

enum ContactMessageStatus: string
{
    case Accepted = 'accepted';
    case Unconfirmed = 'unconfirmed';
    case Confirmed = 'confirmed';
    case Expired = 'expired';
    case Flagged = 'flagged';
}
