<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Controller;
use App\Models\ContactBlacklist;
use App\Rules\RegexPattern;
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
        return ContactBlacklist::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'input' => ['required', Rule::in(['name', 'email'])],
            'pattern' => ['nullable', 'string', 'max:255', new RegexPattern, Rule::unique(ContactBlacklist::class, 'value')],
            'value' => ['nullable', 'string', 'max:255', Rule::when($request->input === 'email', ['email']), Rule::unique(ContactBlacklist::class, 'value')],
        ]);

        foreach (['pattern', 'value'] as $field) {
            if (!empty($validated[$field])) {
                ContactBlacklist::create([
                    'input' => $validated['input'],
                    'value' => $validated[$field],
                ]);
            }
        }

        return response([
            'success' => __('Added to :field blacklist.', [
                'field' => $validated['input'] === 'email' ? 'email address' : 'name'
            ])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactBlacklist $entry)
    {
        return $entry;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactBlacklist $entry)
    {
        return response([
            'success' => __('The value ":value" was removed from the :field blacklist.', [
                'value' => $entry->value,
                'field' => $entry->input === 'email' ? 'email address' : 'name',
            ])
        ], 201);
    }
}
