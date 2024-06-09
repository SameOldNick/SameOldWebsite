<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Rules\ValidBladeIcon;
use Illuminate\Http\Request;

class SkillController extends HomepageController
{
    public function __construct()
    {
        $this->middleware('can:role-edit-profile');
    }

    /**
     * Display skills
     */
    public function index()
    {
        return Skill::all();
    }

    /**
     * Store a new skill
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'icon' => [
                'required',
                'string',
                new ValidBladeIcon,
            ],
            'skill' => 'required|string|max:255',
        ]);

        $skill = Skill::create($validated);

        $this->pageUpdated();

        return $skill;
    }

    /**
     * Display a skill
     */
    public function show(Skill $skill)
    {
        return $skill;
    }

    /**
     * Update a skill
     */
    public function update(Request $request, Skill $skill)
    {
        $request->validate([
            'icon' => [
                'required',
                'string',
                new ValidBladeIcon,
            ],
            'skill' => 'required|string|max:255',
        ]);

        $skill->icon = $request->icon;
        $skill->skill = $request->skill;

        $skill->save();

        $this->pageUpdated();

        return $skill;
    }

    /**
     * Remove a skill
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();

        $this->pageUpdated();

        return [
            'success' => __('Skill ":skill" was removed.', ['skill' => $skill->skill]),
        ];
    }
}
