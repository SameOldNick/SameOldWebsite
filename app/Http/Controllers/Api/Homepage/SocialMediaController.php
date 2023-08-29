<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Rules\SocialMediaLink;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
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

        return $model->value;
    }

    private function getPage()
    {
        return Page::firstWhere(['page' => 'homepage']);
    }
}
