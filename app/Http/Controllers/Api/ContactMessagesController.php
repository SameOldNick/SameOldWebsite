<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-view-contact-messages');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContactMessage::all();
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactMessage $contactMessage)
    {
        return $contactMessage;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|min:1|max:255',
            'email' => 'sometimes|email|max:255',
            'message' => 'sometimes|string',
            'confirmed_at' => 'sometimes|nullable|date',
            'expires_at' => 'sometimes|nullable|date',
        ]);

        foreach ($validated as $key => $value) {
            $contactMessage->{$key} = $value;
        }

        if ($contactMessage->isDirty(array_keys($validated)))
            $contactMessage->save();

        return $contactMessage;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return [
            'success' => __('Contact message was removed.'),
        ];
    }
}
