<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Pages\HomepageController;
use App\Models\SocialMediaLink;
use App\Rules\SocialMediaLink as SocialMediaLinkRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SocialMediaLinkController extends HomepageController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SocialMediaLink::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'link' => [
                'required',
                new SocialMediaLinkRule,
                Rule::unique(SocialMediaLink::class),
            ]
        ]);

        return SocialMediaLink::create(['link' => $request->link]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SocialMediaLink $socialMedium)
    {
        return $socialMedium;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialMediaLink $socialMedium)
    {
        $request->validate([
            'link' => [
                'required',
                new SocialMediaLinkRule,
                Rule::unique(SocialMediaLink::class)->ignore($socialMedium),
            ]
        ]);

        $socialMedium->link = $request->link;

        $socialMedium->save();

        return $socialMedium;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialMediaLink $socialMedium)
    {
        $socialMedium->delete();

        return [
            'success' => __('Social media link ":link" was removed.', ['link' => $socialMedium->link]),
        ];
    }
}
