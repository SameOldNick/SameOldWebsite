<?php

namespace App\Components\Security\Integrations;

use Monolog\Handler\AbstractHandler;
use Monolog\LogRecord;

final class LogHandler extends AbstractHandler
{
    public function handle(LogRecord $record): bool
    {
        // TODO: Handle log messages

        // Returning true causes record to not go to additional handlers.
        return false;
    }
}
