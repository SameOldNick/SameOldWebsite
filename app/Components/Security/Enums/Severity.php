<?php

namespace App\Components\Security\Enums;

enum Severity: string {
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';
}
