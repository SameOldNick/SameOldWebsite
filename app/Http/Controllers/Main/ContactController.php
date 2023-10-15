<?php

namespace App\Http\Controllers\Main;

use App\Components\Settings\ContactPageSettings;
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

        $contacted = new Contacted($request);

        if ($requiresConfirmation) {
            $pendingMessage = (new PendingMessage([
                'message' => $contacted,
            ]))->useDefaultExpiresAt();

            $pendingMessage->save();

            Mail::send(new ConfirmMessage($request, $pendingMessage));

            return view('main.contact', [
                'success' => __('Please check your e-mail for further instructions.'),
                'settings' => $this->getSettings()->toArray(),
            ]);
        } else {
            $admins = Role::firstWhere(['role' => 'admin'])->users;

            Notification::send($admins, new MessageNotification($contacted));

            Mail::send(new ContactedConfirmation($request));

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
        Mail::send($pendingMessage->message);

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
    protected function getSettings() {
        return parent::getSettings()->driver('cache');
    }
}
