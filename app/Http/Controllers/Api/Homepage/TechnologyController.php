<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Pages\HomepageController;
use App\Models\Technology;
use App\Rules\ValidBladeIcon;
use Illuminate\Http\Request;

class TechnologyController extends HomepageController
{
    public function __construct()
    {
        $this->middleware('can:role-edit-profile');
    }

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

        $this->pageUpdated();

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

        $this->pageUpdated();

        return $technology;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technology $technology)
    {
        $technology->delete();

        $this->pageUpdated();

        return [
            'success' => __('Skill ":technology" was removed.', ['technology' => $technology->technology]),
        ];
    }
}
