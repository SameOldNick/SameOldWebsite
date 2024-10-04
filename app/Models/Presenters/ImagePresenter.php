<?php

namespace App\Models\Presenters;

use App\Models\Image;

class ImagePresenter extends Presenter
{
    public function __construct(
        protected readonly Image $image
    ) {}

    /**
     * Gets the image URL
     */
    public function url(): string
    {
        return $this->image->file->is_public ? $this->image->file->presenter()->publicUrl() : $this->image->file->presenter()->privateUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'url' => $this->url(),
        ];
    }
}
