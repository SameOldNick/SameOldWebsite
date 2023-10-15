<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Pages\HomepageController;
use Illuminate\Http\Request;

class MetaDataController extends HomepageController
{
    public function show(Request $request)
    {
        $keys = ['name', 'headline', 'location', 'biography'];

        return $this->getPage()->metaData()->whereIn('key', $keys)->get();
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'headline' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'biography' => 'required|string',
        ]);

        $page = $this->getPage();

        foreach ($validated as $key => $value) {
            $page->metaData()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->pageUpdated();

        return $page->metaData;
    }
}
