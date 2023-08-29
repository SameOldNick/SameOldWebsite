<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use App\Rules\ValidBladeIcon;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Technology::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'icon' => [
                'required',
                'string',
                new ValidBladeIcon,
            ],
            'technology' => 'required|string|max:255',
        ]);

        $technology = Technology::create($validated);

        return $technology;
    }

    /**
     * Display the specified resource.
     */
    public function show(Technology $technology)
    {
        return $technology;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Technology $technology)
    {
        $request->validate([
            'icon' => [
                'required',
                'string',
                new ValidBladeIcon,
            ],
            'technology' => 'required|string|max:255',
        ]);

        $technology->icon = $request->icon;
        $technology->technology = $request->technology;

        $technology->save();

        return $technology;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technology $technology)
    {
        $technology->delete();

        return [
            'success' => __('Skill ":technology" was removed.', ['technology' => $technology->technology]),
        ];
    }
}
