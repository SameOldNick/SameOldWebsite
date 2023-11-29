<?php

namespace App\Components\Security\Responders;

use Throwable;
use App\Components\Security\Issues\Issue;

class StackResponder implements ResponderDriver
{
    public function __construct(
        private readonly array $responders
    )
    {

    }

    public function shouldHandle(Issue $issue): bool {
        return true;
    }

    public function handle(Issue $issue): void {
        foreach ($this->responders as $responder) {
            try {
                if ($responder->shouldHandle($issue)) {
                    $responder->handle($issue);
                }
            } catch (Throwable $caught) {
                report($caught);
            }
        }
    }
}
