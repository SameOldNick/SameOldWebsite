<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Approved = 'approved';

    case Denied = 'denied';

    case Flagged = 'flagged';

    case AwaitingVerification = 'awaiting_verification';

    case AwaitingApproval = 'awaiting_approval';

    case Locked = 'locked';
}
