<?php

namespace App\Http\Controllers\Main;

use App\Components\Moderator\ModerationService;
use App\Events\Contact\ContactSubmissionConfirmed;
use App\Events\Contact\ContactSubmissionRequiresConfirmation;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Traits\Controllers\HasPage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    use HasPage;

    /**
     * Displays contact form
     *
     * @return \Illuminate\Contracts\View\View
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
     * @return \Illuminate\Contracts\View\View
     */
    public function process(ModerationService $moderationService, ContactRequest $request)
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

            if ($requiresConfirmation) {
                $message->useDefaultExpiresAt();
            } else {
                $message->confirmed_at = now();
            }

            $message->save();
        });

        $flags = $moderationService->moderate($message);

        if (! empty($flags)) {
            Log::info("Contact message from '{$request->email}' was flagged.", $flags);

            $message->flags()->saveMany($flags);

            $reason = Arr::first($flags)->reason;

            return response(view('main.contact', [
                'error' => app()->isProduction() ? __('Your contact message has been flagged.') : $reason,
                'settings' => $this->getSettings()->toArray(),
            ]), 422);
        }

        if ($requiresConfirmation) {
            ContactSubmissionRequiresConfirmation::dispatch($message);

            return view('main.contact', [
                'success' => __('Please check your e-mail for further instructions.'),
                'settings' => $this->getSettings()->toArray(),
            ]);
        } else {
            ContactSubmissionConfirmed::dispatch($message);

            return view('main.contact', [
                'success' => __('Thank you for your message! You will receive a reply shortly.'),
                'settings' => $this->getSettings()->toArray(),
            ]);
        }
    }

    /**
     * Confirms senders e-mail address
     *
     * @return mixed
     */
    public function confirm(Request $request, ContactMessage $contactMessage)
    {
        // Check if already approved
        if (! is_null($contactMessage->confirmed_at)) {
            abort(409, __('The confirmation link is no longer valid.'));
        }

        $contactMessage->confirmed_at = now();
        $contactMessage->save();

        ContactSubmissionConfirmed::dispatch($contactMessage);

        return view('main.contact', [
            'success' => __('Thank you for your message! You will receive a reply shortly.'),
            'settings' => $this->getSettings()->toArray(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getPageKey()
    {
        return 'contact';
    }
}
