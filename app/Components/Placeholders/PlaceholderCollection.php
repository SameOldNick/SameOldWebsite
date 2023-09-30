<?php

namespace App\Components\Placeholders;

use ArrayAccess;
use Countable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Iterator;
use IteratorAggregate;
use RuntimeException;

class PlaceholderCollection implements ArrayAccess, Countable, IteratorAggregate, Arrayable
{
    protected $container;

    protected $placeholders;

    /**
     * Inititalizes placeholder collection
     *
     * @param Container $container
     * @param array $placeholders
     */
    public function __construct(Container $container, array $placeholders)
    {
        $this->container = $container;
        $this->placeholders = collect($placeholders);
    }

    /**
     * Checks if placeholder exists.
     *
     * @param string $placeholder
     * @return bool
     */
    public function has(string $placeholder)
    {
        return $this->placeholders->has($placeholder);
    }

    /**
     * Gets placeholders
     *
     * @return array
     */
    public function keys()
    {
        return $this->placeholders->keys();
    }

    /**
     * Gets value for placeholder
     *
     * @param string $placeholder Placeholder
     * @param mixed $default Default value if placeholder doesn't exist
     * @return string
     */
    public function value(string $placeholder, $default = null)
    {
        if (! $this->has($placeholder)) {
            return $default;
        }

        return $this->container->call($this->placeholders[$placeholder]);
    }

    /**
     * Gets number of placeholders
     *
     * @return int
     */
    public function count(): int
    {
        return $this->placeholders->count();
    }

    public function getIterator(): Iterator
    {
        return $this->placeholders->getIterator();
    }

    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Attempt to mutate immutable '.static::class.' object.');
    }

    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Attempt to mutate immutable '.static::class.' object.');
    }

    public function offsetExists($offset): bool
    {
        return isset($this->placeholders[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return isset($this->placeholders[$offset]) ? $this->placeholders[$offset] : null;
    }

    public function toArray()
    {
        return $this->placeholders->all();
    }
}
