<?php

namespace App\Http\Controllers\Main;

use App\Events\Contact\ContactSubmissionApproved;
use App\Events\Contact\ContactSubmissionConfirmed;
use App\Events\Contact\ContactSubmissionRequiresApproval;
use App\Http\Controllers\Pages\ContactController as BaseContactController;
use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

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

        $message = tap(new ContactMessage, function ($message) use ($request, $requiresConfirmation) {
            $message->fill([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
            ]);

            if ($requiresConfirmation)
                $message->useDefaultExpiresAt();
            else
                $message->approved_at = now();

            $message->save();
        });

        if ($requiresConfirmation) {
            ContactSubmissionRequiresApproval::dispatch($message);

            return view('main.contact', [
                'success' => __('Please check your e-mail for further instructions.'),
                'settings' => $this->getSettings()->toArray(),
            ]);
        } else {
            ContactSubmissionApproved::dispatch($message);

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
     * @param ContactMessage $contactMessage
     * @return mixed
     */
    public function confirm(Request $request, ContactMessage $contactMessage)
    {
        // Check if already approved
        if (! is_null($contactMessage->approved_at)) {
            abort(409, __('The confirmation link is no longer valid.'));
        }

        $contactMessage->approved_at = now();
        $contactMessage->save();

        ContactSubmissionConfirmed::dispatch($contactMessage);
        ContactSubmissionApproved::dispatch($contactMessage);

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
