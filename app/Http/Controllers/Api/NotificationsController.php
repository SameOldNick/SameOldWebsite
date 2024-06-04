<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        return $request->user()->notifications;
    }

    /**
     * Gets the read notifications.
     *
     * @return \Illuminate\Support\Collection
     */
    public function read(Request $request)
    {
        return $this->index($request)->whereNotNull('read_at');
    }

    /**
     * Gets the unread notifications.
     *
     * @return \Illuminate\Support\Collection
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
     * Destroys notification.
     *
     * @return Notification
     */
    public function destroy(Request $request, Notification $notification)
    {
        if ($request->user()->isNot($notification->notifiable)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return tap($notification)->delete();
    }
}
