<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Rules\ValidBladeIcon;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Skill::all();
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
                new ValidBladeIcon
            ],
            'skill' => 'required|string|max:255'
        ]);

        $skill = Skill::create($validated);

        return $skill;
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        return $skill;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        $request->validate([
            'icon' => [
                'required',
                'string',
                new ValidBladeIcon
            ],
            'skill' => 'required|string|max:255'
        ]);

        $skill->icon = $request->icon;
        $skill->skill = $request->skill;

        $skill->save();

        return $skill;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();

        return [
            'success' => __('Skill ":skill" was removed.', ['skill' => $skill->skill])
        ];
    }
}
