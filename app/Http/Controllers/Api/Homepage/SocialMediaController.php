<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Events\PageUpdated;
use App\Http\Controllers\Pages\HomepageController;
use App\Rules\SocialMediaLink;
use Illuminate\Http\Request;

class SocialMediaController extends HomepageController
{
    public function show(Request $request)
    {
        $model = $this->getPage()->metaData()->where('key', 'social_media_links')->first();

        return ! is_null($model) ? $model->value : [];
    }

    public function update(Request $request)
    {
        $request->validate([
            'links' => 'array',
            'links.*' => [
                'string',
                new SocialMediaLink,
            ],
        ]);

        $model = $this->getPage()->metaData()->updateOrCreate(
            ['key' => 'social_media_links'],
            ['value' => $request->links]
        );

        $this->pageUpdated();

        return $model->value;
    }
}
