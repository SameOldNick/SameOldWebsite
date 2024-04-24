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

    /**
     * Gets non-ASCII character entropy.
     *
     * @return list<string>
     */
    protected function getNonAsciiEntropy() {
        $chars = [];

        for ($cp = 128; $cp < 255; $cp++) {
            array_push($chars, chr($cp));
        }

        return $chars;
    }

    /**
     * Gets whitespace entropy.
     *
     * @param integer $spaces
     * @param integer $tabs
     * @param integer $newlines
     * @return list<string>
     */
    protected function getWhitespaceEntropy(int $spaces = 1, int $tabs = 1, int $newlines = 1) {
        return str_split(str_repeat(' ', $spaces) . str_repeat("\t", $tabs) . str_repeat("\n", $newlines));
    }
}
