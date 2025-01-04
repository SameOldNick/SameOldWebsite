<?php

namespace Tests\Feature\Main;

use App\Events\Contact\ContactSubmissionConfirmed;
use App\Models\EmailBlacklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\DisablesVite;
use Tests\TestCase;

class ContactModerateTest extends TestCase
{
    use CreatesUser;
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests the contact message is allowed.
     *
     * @return void
     */
    public function test_contact_message_allowed()
    {
        Event::fake();

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertSuccessful();

        Event::assertDispatched(ContactSubmissionConfirmed::class);
    }

    /**
     * Tests the contact email is blacklisted.
     *
     * @return void
     */
    public function test_contact_email_blacklisted()
    {
        Event::fake();

        $email = $this->faker->email;

        EmailBlacklist::create(['email' => $email]);

        $data = [
            'name' => $this->faker->name,
            'email' => $email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', "The email address {$email} is banned.")
            ->assertStatus(422);
    }
}
