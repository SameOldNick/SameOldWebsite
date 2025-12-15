<?php

namespace App\Components\Backup\Enums;

enum NotificationChannels: string
{
    case Mail = 'mail';
    case Slack = 'slack';
    case Discord = 'discord';
    case Ntfy = 'ntfy';
}
