<?php

namespace App\Traits\Models;

trait AppendsPresenter
{
    /**
     * Whether to append presenter data to array.
     */
    protected bool $appendPresenter;

    /**
     * Initializes presenter array.
     */
    public function initializePresenterArray(): array
    {
        return [$this->getPresenterKey() => $this->presenter()->toArray()];
    }

    /**
     * Gets the presenter key
     */
    public function getPresenterKey(): string
    {
        return 'extra';
    }

    /**
     * Whether presenter should be appended.
     *
     * @return boolean
     */
    public function shouldAppendPresenter(): bool
    {
        return $this->appendPresenter ?? false;
    }

    /**
     * Appends presenter data to model array
     */
    public function appendPresenter(bool $enabled = true): static
    {
        $this->appendPresenter = $enabled;

        return $this;
    }

    /**
     * Transforms model to array
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        return $this->shouldAppendPresenter() ? [
            ...$array,
            ...$this->initializePresenterArray(),
        ] : $array;
    }
}
