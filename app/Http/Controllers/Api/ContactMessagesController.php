<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContactMessageStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageCollection;
use App\Models\ContactMessage;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'per_page' => 'nullable|numeric|min:0',
            'sort' => 'sometimes|in:from,sent_ascending,sent_descending',
            'show' => [
                'sometimes',
                Rule::enum(ContactMessageStatus::class),
            ],
        ]);

        $perPage = $request->has('per_page') ? $request->integer('per_page') : null;

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

        if ($request->has('per_page')) {
            $perPage = $request->integer('per_page') ?: $query->count();
        } else {
            $perPage = null;
        }

        return new ContactMessageCollection($query->paginate($perPage));
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

        $updatedMessage = $this->performUpdate($contactMessage, $validated);

        return $updatedMessage;
    }

    /**
     * Updates multiple messages
     *
     * @param Request $request
     * @return mixed
     */
    public function bulkUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'messages' => 'required|array',
            'messages.*.uuid' => [
                'required',
                'uuid',
                Rule::exists(ContactMessage::class)
            ],
            'messages.*.name' => 'sometimes|string|min:1|max:255',
            'messages.*.email' => 'sometimes|email|max:255',
            'messages.*.message' => 'sometimes|string',
            'messages.*.confirmed_at' => 'sometimes|nullable|date',
            'messages.*.expires_at' => 'sometimes|nullable|date',
        ]);

        $updatedMessages = [];

        // Use a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            foreach ($validatedData['messages'] as $data) {
                // Find the contact message by ID
                $contactMessage = ContactMessage::findOrFail($data['uuid']);

                $updatedMessages[] = $this->performUpdate($contactMessage, $data);
            }

            // Commit the transaction if all updates succeed
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to update messages.'], 500);
        }

        return response()->json($updatedMessages);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        if (!$this->performDestroy($contactMessage))
            return response()->json(['error' => 'Failed to delete message.'], 500);

        return [
            'success' => __('Contact message was removed.'),
        ];
    }

    /**
     * Removes multiple messages
     *
     * @param Request $request
     * @return mixed
     */
    public function bulkDestroy(Request $request)
    {
        $validatedData = $request->validate([
            'messages' => 'required|array',
            'messages.*' => [
                'required',
                'uuid',
                Rule::exists(ContactMessage::class, 'uuid')
            ],
        ]);

        // Use a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            foreach ($validatedData['messages'] as $uuid) {
                // Find the contact message by ID
                $contactMessage = ContactMessage::findOrFail($uuid);

                if (!$this->performDestroy($contactMessage))
                    throw new Exception("Deleting message with UUID '{$uuid}' failed.");
            }

            // Commit the transaction if all updates succeed
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to delete messages.'], 500);
        }

        return [
            'success' => __('Contact messages were removed.'),
        ];
    }

    /**
     * Updates a contact message
     *
     * @param ContactMessage $contactMessage
     * @param array $data
     * @return ContactMessage
     */
    protected function performUpdate(ContactMessage $contactMessage, array $data)
    {
        foreach ($data as $key => $value) {
            $contactMessage->{$key} = $value;
        }

        // Save only if there are changes
        if ($contactMessage->isDirty()) {
            $contactMessage->save();
        }

        return $contactMessage;
    }

    /**
     * Deletes a contact message
     *
     * @param ContactMessage $contactMessage
     * @return bool
     */
    protected function performDestroy(ContactMessage $contactMessage)
    {
        return (bool) $contactMessage->delete();
    }
}
