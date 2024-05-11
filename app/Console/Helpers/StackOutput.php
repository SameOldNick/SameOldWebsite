<?php

namespace App\Console\Helpers;

use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class StackOutput extends Output
{
    /**
     * Initializes StackOutput instance
     *
     * @param OutputInterface[] $outputs
     */
    public function __construct(
        protected readonly array $outputs
    ) {
        parent::__construct();
    }

    /**
     * Writes a message to the output.
     *
     * @return void
     */
    protected function doWrite(string $message, bool $newline): void
    {
        foreach ($this->outputs as $output) {
            $output->write($message, $newline);
        }
    }
}
