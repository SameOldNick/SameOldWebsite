<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Tag::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tag' => 'required|string|unique:tags',
            'slug' => 'nullable|string|unique:tags',
        ]);

        $tag = new Tag(['tag' => $request->tag]);

        if ($request->filled('slug')) {
            $tag->slug = $request->slug;
        }

        $tag->save();

        return $tag;
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        return $tag;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'tag' => [
                'required',
                'string',
                Rule::unique('tags')->ignore($tag),
            ],
            'slug' => [
                'nullable',
                'string',
                Rule::unique('tags')->ignore($tag),
            ],
        ]);

        $tag->tag = $request->tag;
        $tag->slug = $request->slug;

        $tag->save();

        return $tag;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return [
            'message' => __('Tag ":tag" has been removed.', ['tag' => $tag->tag]),
        ];
    }
}
