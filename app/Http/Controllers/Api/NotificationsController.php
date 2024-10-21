<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $request->validate([
            'show' => 'sometimes|in:read,unread,all',
            'type' => 'sometimes|string',
        ]);

        $show = $request->str('show', 'all');

        $query = match ((string) $show) {
            'read' => $request->user()->readNotifications(),
            'unread' => $request->user()->unreadNotifications(),
            default => $request->user()->notifications(),
        };

        if ($type = (string) $request->str('type')) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    /**
     * Gets the read notifications.
     *
     * @return \Illuminate\Support\Collection
     *
     * @deprecated
     */
    public function read(Request $request)
    {
        return $this->index($request)->whereNotNull('read_at');
    }

    /**
     * Gets the unread notifications.
     *
     * @return \Illuminate\Support\Collection
     *
     * @deprecated
     */
    public function unread(Request $request)
    {
        return $this->index($request)->whereNull('read_at');
    }

    /**
     * Display the specified resource.
     *
     * @return Notification
     */
    public function show(Request $request, Notification $notification)
    {
        // TODO: Create policy for notifications.
        if ($request->user()->isNot($notification->notifiable)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return $notification;
    }

    /**
     * Marks notification as read.
     *
     * @return Notification
     */
    public function markRead(Request $request, Notification $notification)
    {
        if ($request->user()->isNot($notification->notifiable)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return tap($notification)->markAsRead();
    }

    /**
     * Marks notification as unread.
     *
     * @return Notification
     */
    public function markUnread(Request $request, Notification $notification)
    {
        if ($request->user()->isNot($notification->notifiable)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return tap($notification)->markAsUnread();
    }

    /**
     * Bulk updates notifications
     *
     * @return void
     */
    public function bulkUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'notifications' => 'required|array',
            'notifications.*.id' => [
                'required',
                'uuid',
                Rule::exists(Notification::class),
            ],
            'notifications.*.read_at' => 'nullable|date',
        ]);

        $updated = [];

        // Use a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            foreach ($validatedData['notifications'] as $data) {
                // Find the notification by ID
                $notification = Notification::findOrFail($data['id']);

                if ($request->user()->isNot($notification->notifiable)) {
                    throw new Exception("Notification '{$notification->id}' does not belong to user.");
                }

                $updated[] = $this->performUpdate($notification, $data);
            }

            // Commit the transaction if all updates succeed
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to update notifications.'], 500);
        }

        return response()->json($updated);
    }

    /**
     * Destroys notification.
     *
     * @return Notification
     */
    public function destroy(Request $request, Notification $notification)
    {
        if ($request->user()->isNot($notification->notifiable)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->performDestroy($notification);

        return $notification;
    }

    /**
     * Bulk destroy notifications
     *
     * @return mixed
     */
    public function bulkDestroy(Request $request)
    {
        $validatedData = $request->validate([
            'notifications' => 'required|array',
            'notifications.*' => [
                'required',
                'uuid',
                Rule::exists(Notification::class, 'id'),
            ],
        ]);

        // Use a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            foreach ($validatedData['notifications'] as $id) {
                // Find the notification by ID
                $notification = Notification::findOrFail($id);

                if ($request->user()->isNot($notification->notifiable)) {
                    throw new Exception("Notification '{$notification->id}' does not belong to user.");
                }

                $this->performDestroy($notification);
            }

            // Commit the transaction if all updates succeed
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to update notifications.'], 500);
        }

        return [
            'success' => __('Notifications were removed.'),
        ];
    }

    /**
     * Updates a notification
     *
     * @return Notification
     */
    protected function performUpdate(Notification $notification, array $data)
    {
        foreach ($data as $key => $value) {
            $notification->{$key} = $value;
        }

        // Save only if there are changes
        if ($notification->isDirty()) {
            $notification->save();
        }

        return $notification;
    }

    /**
     * Deletes a notification
     *
     * @return bool
     */
    protected function performDestroy(Notification $notification)
    {
        return (bool) $notification->delete();
    }
}
