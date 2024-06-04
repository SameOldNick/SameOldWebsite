<?php

namespace App\Traits\Support;

use Illuminate\Container\Container;

trait BuildsFromContainer
{
    /**
     * Builds the instance using the container.
     *
     * @return static
     */
    public function build()
    {
        if (method_exists($this, 'doBuild')) {
            // The 'doBuild' method must be public (since it's the Container instance calling it)
            $built = Container::getInstance()->call([$this, 'doBuild']);

            return $built instanceof static ? $built : $this;
        }

        return $this;
    }

    /**
     * Creates the instance.
     *
     * @param  mixed  ...$args  Parameters to pass to constructor
     * @return static
     */
    public static function create(...$args)
    {
        $instance = new static(...$args);

        return $instance->build();
    }
}
