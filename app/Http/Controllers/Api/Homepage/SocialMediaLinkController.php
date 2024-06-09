<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Models\SocialMediaLink;
use App\Rules\SocialMediaLink as SocialMediaLinkRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SocialMediaLinkController extends HomepageController
{
    public function __construct()
    {
        $this->middleware('can:role-edit-profile');
    }

    /**
     * Display social media links
     */
    public function index()
    {
        return SocialMediaLink::all();
    }

    /**
     * Store a new social media link
     */
    public function store(Request $request)
    {
        $request->validate([
            'link' => [
                'required',
                new SocialMediaLinkRule,
                Rule::unique(SocialMediaLink::class),
            ],
        ]);

        return SocialMediaLink::create(['link' => $request->link]);
    }

    /**
     * Display a social media link
     */
    public function show(SocialMediaLink $socialMedium)
    {
        return $socialMedium;
    }

    /**
     * Update a social media link
     */
    public function update(Request $request, SocialMediaLink $socialMedium)
    {
        $request->validate([
            'link' => [
                'required',
                new SocialMediaLinkRule,
                Rule::unique(SocialMediaLink::class)->ignore($socialMedium),
            ],
        ]);

        $socialMedium->link = $request->link;

        $socialMedium->save();

        return $socialMedium;
    }

    /**
     * Remove a social media link
     */
    public function destroy(SocialMediaLink $socialMedium)
    {
        $socialMedium->delete();

        return [
            'success' => __('Social media link ":link" was removed.', ['link' => $socialMedium->link]),
        ];
    }
}
