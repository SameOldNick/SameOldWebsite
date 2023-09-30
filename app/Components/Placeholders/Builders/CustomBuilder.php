<?php

namespace App\Components\Placeholders\Builders;

use Closure;

class CustomBuilder {
    protected $placeholders;

    public function __construct(array $placeholders = [])
    {
        $this->placeholders = collect($placeholders);
    }

    public function has(string $placeholder) {
        return $this->placeholders->has($placeholder);
    }

    public function set(string $placeholder, callable $closure) {
        $this->placeholders[$placeholder] = $closure;

        return $this;
    }

    public function remove(string $placeholder) {
        unset($this->placeholders[$placeholder]);

        return $this;
    }

    public function __invoke() {
        return $this->placeholders;
    }
}
