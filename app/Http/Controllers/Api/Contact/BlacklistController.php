<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Controller;
use App\Models\EmailBlacklist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlacklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-change-contact-settings');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return EmailBlacklist::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique(EmailBlacklist::class)],
        ]);

        EmailBlacklist::create(['email' => $validated['email']]);

        return response(['success' => __('The email address ":email" was blacklisted.', ['email' => $validated['email']])], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailBlacklist $emailBlacklist)
    {
        return $emailBlacklist;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailBlacklist $emailBlacklist)
    {
        return response(['success' => __('The email address ":email" was removed.', ['email' => $emailBlacklist->email])], 201);
    }
}
