<?php

namespace App\Components\Passwords\Concerns;

trait UsesEntropy
{
    /**
     * Gets uppercase entropy.
     *
     * @return list
     */
    protected function getUppercaseEntropy()
    {
        return range('A', 'Z');
    }

    /**
     * Gets lowercase entropy.
     *
     * @return list
     */
    protected function getLowercaseEntropy()
    {
        return range('a', 'z');
    }

    /**
     * Gets number entropy.
     *
     * @return list
     */
    protected function getNumberEntropy()
    {
        return range('0', '9');
    }

    /**
     * Gets symbol entropy.
     *
     * @return list
     */
    protected function getSymbolEntropy()
    {
        return [
            '!', '"', '#', '$', '%', '&', "'", '(', ')', '*', '+', ',', '-', '.', '/',
            ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|', '}', '~',
        ];
    }
}
