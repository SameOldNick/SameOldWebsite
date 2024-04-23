<?php

namespace App\Components\Passwords\Concerns;

use App\Components\Passwords\Generator\Generator;
use App\Components\Passwords\Generator\Options as GenerateOptions;

trait GeneratesPassword {
    protected ?GenerateOptions $generateUsing;

    /**
     * Sets options to generate options by default.
     *
     * @param GenerateOptions|null $options
     * @return $this
     */
    public function generateUsing(?GenerateOptions $options) {
        $this->generateUsing = $options;

        return $this;
    }

    /**
     * Generates password
     *
     * @param GenerateOptions|null $options Options to use. If null, the default options are used.
     * @return string
     */
    public function generate(?GenerateOptions $options = null): string {
        $generator = $this->getGenerator($options);

        return $generator->generate();
    }

    /**
     * Gets generator to generate password.
     *
     * @param GenerateOptions|null $options
     * @return Generator
     */
    public function getGenerator(?GenerateOptions $options = null) {
        if (is_null($options)) {
            if (!is_null($this->generateUsing)) {
                $options = $this->generateUsing;
            } else {
                $options = GenerateOptions::default();
            }
        }

        return new Generator($options);
    }
}
