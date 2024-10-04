<?php

namespace App\Traits\Models;

use RuntimeException;

/**
 * @template TPresenter of \App\Models\Presenters\Presenter
 */
trait HasPresenter
{
    use AppendsPresenter;

    /**
     * Get a new presenter instance
     *
     * @return TPresenter
     *
     * @throws RuntimeException Thrown if presenter class is not found.
     */
    public function presenter()
    {
        $presenter = $this->presenterClass();

        if (! $presenter) {
            throw new RuntimeException('Presenter class not found.');
        }

        return $this->newPresenter($presenter);
    }

    /**
     * Get the presenter class for the model.
     *
     * @return class-string<TPresenter>|null
     */
    protected function presenterClass(): ?string
    {
        if (isset(static::$presenter)) {
            return static::$presenter;
        }

        return null;
    }

    /**
     * Creates new presenter instance.
     *
     * @param  class-string<TPresenter>  $class
     * @return TPresenter
     */
    protected function newPresenter(string $class)
    {
        $presenter = new $class($this);

        return $presenter;
    }
}
