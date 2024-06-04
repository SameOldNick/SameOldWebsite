<?php

namespace App\Components\Websockets\Console;

use App\Components\Websockets\Notifiers\ProcessNotifier;
use Symfony\Component\Console\Output\Output;

class OutputRedirector extends Output
{
    /**
     * Initializes OutputRedirector instance.
     *
     * @param  ProcessNotifier  $notifier  Used to send output as notification.
     */
    public function __construct(
        public readonly ProcessNotifier $notifier
    ) {
        // Setting decorated to true causes output to be in terminal format.
        parent::__construct(decorated: true);
    }

    /**
     * {@inheritDoc}
     */
    public function writeln(string|iterable $messages, int $options = self::OUTPUT_NORMAL): void
    {
        parent::writeln($messages);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string|iterable $messages, bool $newline = false, int $options = self::OUTPUT_NORMAL): void
    {
        parent::write($messages, $newline);
    }

    /**
     * {@inheritDoc}
     */
    protected function doWrite(string $message, bool $newline): void
    {
        $this->notifier->output($message, $newline);
    }
}
