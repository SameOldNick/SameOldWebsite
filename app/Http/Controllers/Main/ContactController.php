<?php

namespace App\Http\Controllers\Main;

use App\Components\Settings\ContactPageSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Jobs\ContactFormNotifier;
use App\Mail\ConfirmMessage;
use App\Mail\Contacted;
use App\Mail\ContactedConfirmation;
use App\Models\PendingMessage;
use App\Models\Role;
use App\Notifications\MessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    /**
     * Displays contact form
     *
     * @param Request $request
     * @param ContactPageSettings $settings
     * @return mixed
     */
    public function show(Request $request, ContactPageSettings $settings) {
        $data = [
            'settings' => $settings->toArray()
        ];

        return view('main.contact', $data);
    }

    /**
     * Processes contact form submission.
     *
     * @param ContactRequest $request
     * @param ContactPageSettings $settings
     * @return mixed
     */
    public function process(ContactRequest $request, ContactPageSettings $settings) {
        $requiresConfirmation = false;

        if ($settings->setting('require_confirmation')) {
            $user = $request->email === optional($request->user())->email ? $request->user() : null;
            $requiredBy = $settings->setting('confirmation_required_by');

            if ($requiredBy == 'all_users') {
                $requiresConfirmation = true;
            } else if ($requiredBy == 'unregistered_users') {
                $requiresConfirmation = is_null($user);
            } else if ($requiredBy == 'unregistered_unverified_users') {
                $requiresConfirmation = is_null($user) || !$user->hasVerifiedEmail();
            }
        }

        $contacted = new Contacted($request);

        if ($requiresConfirmation) {
            $pendingMessage = (new PendingMessage([
                'message' => $contacted
            ]))->useDefaultExpiresAt();

            $pendingMessage->save();

            Mail::send(new ConfirmMessage($request, $pendingMessage));

            return view('main.contact', [
                'success' => __('Please check your e-mail for further instructions.'),
                'settings' => $settings->toArray()
            ]);
        } else {
            $admins = Role::firstWhere(['role' => 'admin'])->users;

            Notification::send($admins, new MessageNotification($contacted));

            Mail::send(new ContactedConfirmation($request));

            return view('main.contact', [
                'success' => __('Thank you for your message! You will receive a reply shortly.'),
                'settings' => $settings->toArray()
            ]);
        }

    }

    /**
     * Confirms senders e-mail address
     *
     * @param Request $request
     * @param PendingMessage $pendingMessage
     * @param ContactPageSettings $settings
     * @return mixed
     */
    public function confirm(Request $request, PendingMessage $pendingMessage, ContactPageSettings $settings) {
        Mail::send($pendingMessage->message);

        return view('main.contact', [
            'success' => __('Thank you for your message! You will receive a reply shortly.'),
            'settings' => $settings->toArray()
        ]);
    }
}
