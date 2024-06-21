<?php

namespace App\Components\Passwords\Generator;

use App\Components\Passwords\Concerns\UsesEntropy;
use Illuminate\Support\Arr;

/**
 * Generates a password
 *
 * @uses UsesEntropy
 */
final class Generator
{
    use UsesEntropy;

    /**
     * Initializes generator
     */
    public function __construct(
        protected readonly Options $options
    ) {}

    /**
     * Generates password
     */
    public function generate(): string
    {
        $length = $this->generateLength();

        $required = $this->generateRequired();

        if (count($required) >= $length) {
            return $this->arrayToString($this->shuffle($required), $length);
        }

        return $this->arrayToString($this->shuffle($this->fill($required, $length)));
    }

    /**
     * Generates random length for password
     */
    protected function generateLength(): int
    {
        [$start, $end] = $this->options->getBounds();

        return $this->randomNumber($start, $end);
    }

    /**
     * Generates required characters for password
     *
     * @return array Array of characters
     */
    protected function generateRequired(): array
    {
        $required = [
            ...$this->generateUppercase($this->options->uppercase),
            ...$this->generateLowercase($this->options->lowercase),
            ...$this->generateNumbers($this->options->numbers),
            ...$this->generateSymbols($this->options->symbols),
        ];

        return $required;
    }

    /**
     * Fills array with remaining characters
     */
    protected function fill(array $items, int $length): array
    {
        $entropy = [
            ...$this->getUppercaseEntropy(),
            ...$this->getLowercaseEntropy(),
            ...$this->getNumberEntropy(),
            ...$this->getSymbolEntropy(),
        ];

        if (! $this->options->ascii) {
            array_push($entropy, ...$this->getNonAsciiEntropy());
        }

        if ($this->options->whitespaces['spaces'] || $this->options->whitespaces['tabs'] || $this->options->whitespaces['newlines']) {
            array_push($entropy, ...$this->getWhitespaceEntropy(
                spaces: $this->options->whitespaces['spaces'] ? 1 : 0,
                tabs: $this->options->whitespaces['tabs'] ? 1 : 0,
                newlines: $this->options->whitespaces['newlines'] ? 1 : 0,
            ));
        }

        array_push($items, ...$this->pickFrom($entropy, $length - count($items)));

        return $items;
    }

    /**
     * Generates upper case characters
     */
    protected function generateUppercase(int $count): array
    {
        return $this->pickFrom($this->getUppercaseEntropy(), $count);
    }

    /**
     * Generates lowercase letters
     */
    protected function generateLowercase(int $count): array
    {
        return $this->pickFrom($this->getLowercaseEntropy(), $count);
    }

    /**
     * Generates numbers
     */
    protected function generateNumbers(int $count): array
    {
        return $this->pickFrom($this->getNumberEntropy(), $count);
    }

    /**
     * Generates symbols
     */
    protected function generateSymbols(int $count): array
    {
        return $this->pickFrom($this->getSymbolEntropy(), $count);
    }

    /**
     * Picks random items
     *
     * @param  array  $pool  Where to pick from
     * @param  int  $count  Number of items to pick
     * @param  bool  $allowDuplicates  Whether to allow duplicates in picked (default: true)
     */
    protected function pickFrom(array $pool, int $count, bool $allowDuplicates = true): array
    {
        // Make sure items is a list (indices)
        $items = array_values($pool);

        if ($allowDuplicates) {
            $picked = [];

            while (count($picked) < $count) {
                // Securely generates random number for index
                $key = $this->randomNumber(0, count($items) - 1);

                array_push($picked, $items[$key]);
            }

            return $picked;
        } else {
            return Arr::random($items, $count);
        }
    }

    /**
     * Shuffle the items
     *
     * @return array
     */
    protected function shuffle(array $items)
    {
        shuffle($items);

        return $items;
    }

    /**
     * Converts array to string
     *
     * @param  int|null  $count  Length of output. If null, the output is the same length as items. (default: null)
     */
    protected function arrayToString(array $items, ?int $count = null): string
    {
        return implode(! is_null($count) ? array_slice($items, 0, $count) : $items);
    }

    /**
     * Securely generates random number
     *
     * @param  int  $start  Start number (inclusive)
     * @param  int  $end  End number (inclusive)
     */
    protected function randomNumber(int $start, int $end): int
    {
        return random_int($start, $end);
    }
}
