<?php

namespace App\Http\Controllers\Api\Contact;

use App\Events\PageUpdated;
use App\Http\Controllers\Pages\ContactController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MetaDataController extends ContactController
{
    public function show()
    {
        $keys = [
            'sender_replyto',
            'sender_subject',
            'sender_message',
            'recipient_email',
            'recipient_subject',
            'recipient_template',
            'require_recaptcha',
            'require_confirmation',
            'confirmation_required_by',
            'confirmation_subject',
            'honeypot_field',
            'honeypot_field_name',
        ];

        return $this->getPage()->metaData()->whereIn('key', $keys)->get();
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'sender_replyto' => 'required|email|max:255',
            'sender_subject' => 'required|string|max:255',
            'sender_message' => 'required|string',
            'recipient_email' => 'required|email|max:255',
            'recipient_subject' => 'required|string|max:255',
            'recipient_template' => 'required|string',
            'require_recaptcha' => 'required|boolean',
            'require_confirmation' => 'required|boolean',
            'confirmation_required_by' => [
                'exclude_unless:require_confirmation,true',
                'required',
                Rule::in(['all_users', 'unregistered_users', 'unregistered_unverified_users']),
            ],
            'confirmation_subject' => 'exclude_unless:require_confirmation,true|required|string|max:255',
            'honeypot_field' => 'required|boolean',
            'honeypot_field_name' => 'exclude_unless:honeypot_field,true|required|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            $this->getPage()->metaData()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return $this->pageUpdated()->getPage()->metaData;
    }
}
