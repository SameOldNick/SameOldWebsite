<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class ContactMessagesControllerTest extends TestCase
{
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;
    use WithRoles;

    /**
     * Tests contact message name is updated.
     *
     * @return void
     */
    public function test_message_name_updated()
    {
        $message = ContactMessage::factory()->requiresConfirmation()->create();

        $name = $this->faker()->name;

        $response = $this->withRoles(['view_contact_messages'])->putJson(sprintf('/api/contact-messages/%s', $message->getKey()), [
            'name' => $name,
        ]);

        $response->assertSuccessful();

        $updated = $message->fresh();

        $this->assertEquals($name, $updated->name);

        // Check other fields are unchanged
        $this->assertEquals($message->email, $updated->email);
        $this->assertEquals($message->message, $updated->message);
    }

    /**
     * Tests contact message e-mail is updated.
     *
     * @return void
     */
    public function test_message_email_updated()
    {
        $message = ContactMessage::factory()->requiresConfirmation()->create();

        $email = $this->faker()->email;

        $response = $this->withRoles(['view_contact_messages'])->putJson(sprintf('/api/contact-messages/%s', $message->getKey()), [
            'email' => $email,
        ]);

        $response->assertSuccessful();

        $updated = $message->fresh();

        $this->assertEquals($email, $updated->email);

        // Check other fields are unchanged
        $this->assertEquals($message->name, $updated->name);
        $this->assertEquals($message->message, $updated->message);
    }

    /**
     * Tests contact message is updated.
     *
     * @return void
     */
    public function test_message_updated()
    {
        $message = ContactMessage::factory()->requiresConfirmation()->create();

        $text = $this->faker()->realText();

        $response = $this->withRoles(['view_contact_messages'])->putJson(sprintf('/api/contact-messages/%s', $message->getKey()), [
            'message' => $text,
        ]);

        $response->assertSuccessful();

        $updated = $message->fresh();

        $this->assertEquals($text, $updated->message);

        // Check other fields are unchanged
        $this->assertEquals($message->email, $updated->email);
        $this->assertEquals($message->name, $updated->name);
    }

    /**
     * Tests contact message is set as confirmed.
     *
     * @return void
     */
    public function test_mark_message_confirmed()
    {
        $message = ContactMessage::factory()->requiresConfirmation()->create();

        $response = $this->withRoles(['view_contact_messages'])->putJson(sprintf('/api/contact-messages/%s', $message->getKey()), [
            'confirmed_at' => now(),
        ]);

        $response->assertSuccessful();

        $this->assertNotNull($message->refresh()->confirmed_at);
    }

    /**
     * Tests contact message is set as unconfirmed.
     *
     * @return void
     */
    public function test_mark_message_unconfirmed()
    {
        $message = ContactMessage::factory()->confirmed()->requiresConfirmation()->create();

        $response = $this->withRoles(['view_contact_messages'])->putJson(sprintf('/api/contact-messages/%s', $message->getKey()), [
            'confirmed_at' => null,
        ]);

        $response->assertSuccessful();

        $this->assertNull($message->refresh()->confirmed_at);
    }

    /**
     * Tests contact message is removed.
     *
     * @return void
     */
    public function test_remove_contact_message()
    {
        $message = ContactMessage::factory()->confirmed()->requiresConfirmation()->create();

        $response = $this->withRoles(['view_contact_messages'])->deleteJson(sprintf('/api/contact-messages/%s', $message->getKey()));

        $response->assertSuccessful();

        $this->assertNull($message->fresh());
    }
}
