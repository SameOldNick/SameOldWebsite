<?php

namespace Database\Seeders\Setup;

use App\Models\Page;
use App\Models\PageMetaData;
use Illuminate\Database\Seeder;

class ContactPageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $senderMessage = <<<'END'
Thank you for reaching out. I'll be in touch shortly.

In the meantime, here's a Chuck Norris fact: [chuck-norris-fact]
END;

        $recipientTemplate = <<<'END'
Date: [datetime]
Subject: [subject]

Message:
[message]

User Agent: [user-agent]
IP Address: [ip-address]
END;

        $defaults = [
            'sender_replyto' => 'noreply@sameoldnick.com',
            'sender_subject' => 'Your message has been received!',
            'sender_message' => $senderMessage,
            'recipient_email' => 'nick@sameoldnick.com',
            'recipient_subject' => 'Message Received',
            'recipient_template' => $recipientTemplate,
            'require_recaptcha' => false,
            'require_confirmation' => true,
            'confirmation_required_by' => 'all_users',
            'confirmation_subject' => 'Confirmation Required',
            'honeypot_field' => false,
            'honeypot_field_name' => 'is_robot',
        ];

        $models = collect($defaults)->map(fn ($value, $key) => new PageMetaData(['key' => $key, 'value' => $value]));

        Page::firstWhere(['page' => 'contact'])->metaData()->saveMany($models);
    }
}
