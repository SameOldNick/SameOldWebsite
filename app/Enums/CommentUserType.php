<?php

namespace App\Enums;

enum CommentUserType: string
{
    case Registered = 'registered';
    case Guest = 'guest';
}
