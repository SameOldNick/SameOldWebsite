<?php

namespace App\Traits\Models;

use RuntimeException;

/**
 * @template TPresenter of \App\Models\Presenters\Presenter
 */
trait HasPresenter
{
    /**
     * Whether to append presenter data to array.
     *
     * @var boolean
     */
    protected bool $appendPresenter = false;

    /**
     * Get a new presenter instance
     *
     * @return TPresenter
     * @throws RuntimeException Thrown if presenter class is not found.
     */
    public function presenter()
    {
        $presenter = $this->presenterClass();

        if (!$presenter) {
            throw new RuntimeException('Presenter class not found.');
        }

        return $this->newPresenter($presenter);
    }

    /**
     * Initializes presenter array.
     */
    public function initializePresenterArray(): array
    {
        return [$this->getPresenterKey() => $this->presenter()->toArray()];
    }

    /**
     * Gets the presenter key
     *
     * @return string
     */
    public function getPresenterKey(): string
    {
        return 'presenter';
    }

    /**
     * Appends presenter data to model array
     *
     * @param boolean $enabled
     * @return static
     */
    public function appendPresenter(bool $enabled = true): static
    {
        $this->appendPresenter = $enabled;

        return $this;
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
     * @param class-string<TPresenter> $class
     * @return TPresenter
     */
    protected function newPresenter(string $class)
    {
        $presenter = new $class($this);

        return $presenter;
    }

    public function toArray()
    {
        $array = parent::toArray();

        return $this->appendPresenter ? [
            ...$array,
            ...$this->initializePresenterArray(),
        ] : $array;
    }
}
