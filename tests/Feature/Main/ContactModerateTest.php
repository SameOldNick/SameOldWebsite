<?php

namespace Tests\Feature\Main;

use App\Events\Contact\ContactSubmissionConfirmed;
use App\Models\ContactBlacklist;
use App\Models\EmailBlacklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
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

        ContactBlacklist::create([
            'input' => 'email',
            'value' => $email
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', fn($value) => Str::contains($value, ' is banned.'))
            ->assertStatus(422);
    }

    /**
     * Tests the contact email matches blacklisted pattern.
     *
     * @return void
     */
    public function test_contact_email_pattern_blacklisted()
    {
        Event::fake();

        $domain = $this->faker->freeEmailDomain;
        $email = $this->faker->userName . '@' . $domain;

        $pattern = '/\@' . preg_quote($domain) . '$/';

        ContactBlacklist::create([
            'input' => 'email',
            'value' => $pattern
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', fn($value) => Str::contains($value, ' is banned.'))
            ->assertStatus(422);
    }

    /**
     * Tests the contact name is blacklisted.
     *
     * @return void
     */
    public function test_contact_name_blacklisted()
    {
        Event::fake();

        $name = $this->faker->name;

        ContactBlacklist::create([
            'input' => 'name',
            'value' => $name
        ]);

        $data = [
            'name' => $name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', fn($value) => Str::contains($value, ' is banned.'))
            ->assertStatus(422);
    }

    /**
     * Tests the contact name matches blacklisted pattern.
     *
     * @return void
     */
    public function test_contact_name_pattern_blacklisted()
    {
        Event::fake();

        $name = $this->faker->name;

        $pattern = '/^' . preg_quote($name) . '$/';

        ContactBlacklist::create([
            'input' => 'name',
            'value' => $pattern
        ]);

        $data = [
            'name' => $name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', fn($value) => Str::contains($value, ' is banned.'))
            ->assertStatus(422);
    }
}
