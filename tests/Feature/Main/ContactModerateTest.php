<?php

namespace Tests\Feature\Main;

use App\Enums\ContactMessageStatus;
use App\Models\ContactBlacklist;
use App\Models\ContactMessage;
use App\Models\ContactMessageFlag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
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

        $this->assertNotEquals(ContactMessageStatus::Flagged, ContactMessage::first()->status);
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
            'value' => $email,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', fn ($value) => Str::contains($value, ' is banned.'))
            ->assertStatus(422);

        $this->assertEquals(ContactMessageStatus::Flagged, ContactMessage::first()->status);

        $flag = ContactMessageFlag::first();

        $this
            ->assertModelExists($flag)
            ->assertTrue(Str::contains($flag->reason, $email));
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
        $email = $this->faker->userName.'@'.$domain;

        $pattern = '/\@'.preg_quote($domain).'$/';

        ContactBlacklist::create([
            'input' => 'email',
            'value' => $pattern,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', fn ($value) => Str::contains($value, ' is banned.'))
            ->assertStatus(422);

        $this->assertEquals(ContactMessageStatus::Flagged, ContactMessage::first()->status);

        $flag = ContactMessageFlag::first();

        $this
            ->assertModelExists($flag)
            ->assertTrue(Str::contains($flag->reason, $email));
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
            'value' => $name,
        ]);

        $data = [
            'name' => $name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', fn ($value) => Str::contains($value, ' is banned.'))
            ->assertStatus(422);

        $this->assertEquals(ContactMessageStatus::Flagged, ContactMessage::first()->status);

        $flag = ContactMessageFlag::first();

        $this
            ->assertModelExists($flag)
            ->assertTrue(Str::contains($flag->reason, $name));
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

        $pattern = '/^'.preg_quote($name).'$/';

        ContactBlacklist::create([
            'input' => 'name',
            'value' => $pattern,
        ]);

        $data = [
            'name' => $name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertViewHas('error', fn ($value) => Str::contains($value, ' is banned.'))
            ->assertStatus(422);

        $this->assertEquals(ContactMessageStatus::Flagged, ContactMessage::first()->status);

        $flag = ContactMessageFlag::first();

        $this
            ->assertModelExists($flag)
            ->assertTrue(Str::contains($flag->reason, $name));
    }
}
