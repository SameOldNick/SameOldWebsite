<?php

namespace App\Http\Controllers\Main;

use App\Events\Contact\ContactSubmissionApproved;
use App\Events\Contact\ContactSubmissionRequiresApproval;
use App\Events\Contact\ContactSubmissionConfirmed;
use App\Http\Controllers\Pages\ContactController as BaseContactController;
use App\Http\Requests\ContactRequest;
use App\Mail\ConfirmMessage;
use App\Mail\Contacted;
use App\Mail\ContactedConfirmation;
use App\Models\PendingMessage;
use App\Models\Role;
use App\Notifications\MessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ContactController extends BaseContactController
{
    /**
     * Displays contact form
     *
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request)
    {
        $data = [
            'settings' => $this->getSettings()->toArray(),
        ];

        return view('main.contact', $data);
    }

    /**
     * Processes contact form submission.
     *
     * @param ContactRequest $request
     * @return mixed
     */
    public function process(ContactRequest $request)
    {
        $requiresConfirmation = false;

        if ($this->getSettings()->setting('require_confirmation')) {
            $user = $request->email === optional($request->user())->email ? $request->user() : null;
            $requiredBy = $this->getSettings()->setting('confirmation_required_by');

            if ($requiredBy == 'all_users') {
                $requiresConfirmation = true;
            } elseif ($requiredBy == 'unregistered_users') {
                $requiresConfirmation = is_null($user);
            } elseif ($requiredBy == 'unregistered_unverified_users') {
                $requiresConfirmation = is_null($user) || ! $user->hasVerifiedEmail();
            }
        }

        if ($requiresConfirmation) {
            ContactSubmissionRequiresApproval::dispatch($request->name, $request->email, $request->message);

            return view('main.contact', [
                'success' => __('Please check your e-mail for further instructions.'),
                'settings' => $this->getSettings()->toArray(),
            ]);
        } else {
            ContactSubmissionApproved::dispatch($request->name, $request->email, $request->message);

            return view('main.contact', [
                'success' => __('Thank you for your message! You will receive a reply shortly.'),
                'settings' => $this->getSettings()->toArray(),
            ]);
        }
    }

    /**
     * Confirms senders e-mail address
     *
     * @param Request $request
     * @param PendingMessage $pendingMessage
     * @return mixed
     */
    public function confirm(Request $request, PendingMessage $pendingMessage)
    {
        ContactSubmissionConfirmed::dispatch($pendingMessage);
        ContactSubmissionApproved::dispatch($pendingMessage->name, $pendingMessage->email, $pendingMessage->message);

        return view('main.contact', [
            'success' => __('Thank you for your message! You will receive a reply shortly.'),
            'settings' => $this->getSettings()->toArray(),
        ]);
    }

    /**
     * Gets Page Settings.
     *
     * @return PageSettings
     */
    protected function getSettings()
    {
        return parent::getSettings()->driver('cache');
    }
}
