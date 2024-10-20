<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContactMessageStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageCollection;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactMessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-view-contact-messages');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'sort' => 'sometimes|in:from,sent_ascending,sent_descending',
            'show' => [
                'sometimes',
                Rule::enum(ContactMessageStatus::class),
            ],
        ]);

        $query = ContactMessage::query();

        $show = $request->enum('show', ContactMessageStatus::class);
        $sort = (string) $request->str('sort', 'sent_ascending');

        $sorters = [
            'from' => fn (Builder $query) => $query->orderBy('email'),
            'sent_ascending' => fn (Builder $query) => $query->orderBy('created_at', 'asc'),
            'sent_descending' => fn (Builder $query) => $query->orderBy('created_at', 'desc'),
        ];

        if (isset($sorters[$sort])) {
            $sorters[$sort]($query);
        }

        if ($show) {
            $query->afterQuery(function (Collection $found) use ($show) {
                return $found->filter(fn (ContactMessage $contactMessage) => $contactMessage->status === $show);
            });
        }

        return new ContactMessageCollection($query->paginate());
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

        if ($contactMessage->isDirty(array_keys($validated))) {
            $contactMessage->save();
        }

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
