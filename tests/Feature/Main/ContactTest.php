<?php

namespace Tests\Feature\Main;

use App\Components\Settings\Facades\PageSettings;
use App\Events\Contact\ContactSubmissionConfirmed;
use App\Events\Contact\ContactSubmissionRequiresConfirmation;
use App\Mail\ConfirmMessage;
use App\Mail\Contacted;
use App\Mail\ContactedConfirmation;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\MessageNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\DisablesVite;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use CreatesUser;
    use DisablesVite;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests the correct event is fired for an approved contact submission.
     *
     * @return void
     */
    public function testContactSubmissionConfirmedEventFired()
    {
        Event::fake();

        PageSettings::fake([
            'contact' => [
                'require_confirmation' => false,
            ]
        ]);

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
     * Tests the correct event is fired for a message that requires confirmation.
     *
     * @return void
     */
    public function testContactSubmissionRequiresConfirmationEventFired()
    {
        Event::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertSuccessful();

        Event::assertDispatched(ContactSubmissionRequiresConfirmation::class);
    }

    /**
     * Test logic for guest submitting contact form with confirmation requirement
     *
     * @return void
     */
    public function testGuestContactFormSubmissionRequiresConfirmation()
    {
        Mail::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'all_users',
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->assertGuest()
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Please check your e-mail for further instructions.'));

        Mail::assertSent(ConfirmMessage::class, fn (ConfirmMessage $mail) => $mail->hasTo($data['email']));
    }

    /**
     * Test logic for authenticated user submitting contact form with confirmation requirement
     *
     * @return void
     */
    public function testAuthenticatedUserContactFormSubmissionRequiresConfirmation()
    {
        Mail::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'all_users',
        ]);

        $data = [
            'name' => $this->user->getDisplayName(),
            'email' => $this->user->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->actingAs($this->user)
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Please check your e-mail for further instructions.'));

        Mail::assertSent(ConfirmMessage::class, fn (ConfirmMessage $mail) => $mail->hasTo($data['email']));
    }

    /**
     * Test logic for guest submitting contact form with unregistered user confirmation requirement
     *
     * @return void
     */
    public function testGuestContactFormSubmissionRequiresUnregisteredUserConfirmation()
    {
        Mail::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_users',
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->assertGuest()
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Please check your e-mail for further instructions.'));

        Mail::assertSent(ConfirmMessage::class, fn (ConfirmMessage $mail) => $mail->hasTo($data['email']));
    }

    /**
     * Test logic for authenticated user submitting contact form with unregistered user confirmation requirement
     *
     * @return void
     */
    public function testAuthenticatedUserContactFormSubmissionRequiresUnregisteredUserConfirmation()
    {
        Mail::fake();
        Notification::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_users',
            'recipient_email' => $this->faker->email,
        ]);

        $data = [
            'name' => $this->user->getDisplayName(),
            'email' => $this->user->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->actingAs($this->user)
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Thank you for your message! You will receive a reply shortly.'));

        Mail::assertSent(ContactedConfirmation::class, fn (ContactedConfirmation $mail) => $mail->hasTo($data['email']));
        Notification::assertSentTo($this->admin, MessageNotification::class, fn ($notification) => $notification->mailable instanceof Contacted);
    }

    /**
     * Test logic for guest submitting contact form with unregistered or unverified user confirmation requirement
     *
     * @return void
     */
    public function testGuestContactFormSubmissionRequiresUnregisteredOrUnverifiedUserConfirmation()
    {
        Mail::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_unverified_users',
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->assertGuest()
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Please check your e-mail for further instructions.'));

        Mail::assertSent(ConfirmMessage::class, fn (ConfirmMessage $mail) => $mail->hasTo($data['email']));
    }

    /**
     * Test logic for unverified user submitting contact form with unregistered or unverified user confirmation requirement
     *
     * @return void
     */
    public function testUnverifiedUserContactFormSubmissionRequiresUnregisteredOrUnverifiedUserConfirmation()
    {
        Mail::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_unverified_users',
        ]);

        $user = User::factory()->unverified()->create();

        $data = [
            'name' => $user->getDisplayName(),
            'email' => $user->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->actingAs($user)
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Please check your e-mail for further instructions.'));

        Mail::assertSent(ConfirmMessage::class, fn (ConfirmMessage $mail) => $mail->hasTo($data['email']));
    }

    /**
     * Test logic for verified user submitting contact form with unregistered or unverified user confirmation requirement
     *
     * @return void
     */
    public function testVerifiedUserContactFormSubmissionRequiresUnregisteredOrUnverifiedUserConfirmation()
    {
        Mail::fake();
        Notification::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_unverified_users',
            'recipient_email' => $this->faker->email,
        ]);

        $data = [
            'name' => $this->user->getDisplayName(),
            'email' => $this->user->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->actingAs($this->user)
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Thank you for your message! You will receive a reply shortly.'));

        Mail::assertSent(ContactedConfirmation::class, fn (ContactedConfirmation $mail) => $mail->hasTo($data['email']));
        Notification::assertSentTo($this->admin, MessageNotification::class, fn ($notification) => $notification->mailable instanceof Contacted);
    }

    /**
     * Test logic for guest submitting contact form with no confirmation requirement
     *
     * @return void
     */
    public function testGuestContactFormSubmissionNoConfirmationRequired()
    {
        Mail::fake();
        Notification::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => false,
            'recipient_email' => $this->faker->email,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->assertGuest()
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Thank you for your message! You will receive a reply shortly.'));

        Mail::assertSent(ContactedConfirmation::class, fn (ContactedConfirmation $mail) => $mail->hasTo($data['email']));
        Notification::assertSentTo($this->admin, MessageNotification::class, fn ($notification) => $notification->mailable instanceof Contacted);
    }

    /**
     * Test logic for authenticated user submitting contact form with no confirmation requirement
     *
     * @return void
     */
    public function testAuthenticatedUserContactFormSubmissionNoConfirmationRequired()
    {
        Mail::fake();
        Notification::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => false,
            'recipient_email' => $this->faker->email,
        ]);

        $data = [
            'name' => $this->user->getDisplayName(),
            'email' => $this->user->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $response =
            $this
                ->actingAs($this->user)
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Thank you for your message! You will receive a reply shortly.'));

        Mail::assertSent(ContactedConfirmation::class, fn (ContactedConfirmation $mail) => $mail->hasTo($data['email']));
        Notification::assertSentTo($this->admin, MessageNotification::class, fn ($notification) => $notification->mailable instanceof Contacted);
    }

    /**
     * Test logic for verifying email following contact form submission.
     *
     * @return void
     */
    public function testVerifyEmailFollowingSubmission()
    {
        Mail::fake();
        Notification::fake();

        PageSettings::fake('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'all_users',
            'recipient_email' => $this->faker->email,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Please check your e-mail for further instructions.'));

        [$mailable] = Mail::sent(ConfirmMessage::class, fn (ConfirmMessage $mailable) => $mailable->hasTo($data['email']));

        $content = $mailable->content();

        $this->assertArrayHasKey('url', $content->with);

        $this
            ->get($content->with['url'])
            ->assertSuccessful();

        Notification::assertSentTo($this->admin, MessageNotification::class, fn ($notification) => $notification->mailable instanceof Contacted);
    }

    /**
     * Test logic for verifying email following contact form submission.
     *
     * @return void
     */
    public function testContactEmailConfirmationEventFired()
    {
        Event::fake();

        $contactMessage = ContactMessage::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ])->useDefaultExpiresAt();

        $contactMessage->save();

        $this
            ->get($contactMessage->generateUrl())
            ->assertSuccessful();

        Event::assertDispatched(ContactSubmissionConfirmed::class);
    }

    /**
     * Test logic for confirming email with expired link following contact form submission.
     *
     * @return void
     */
    public function testContactEmailConfirmationExpired()
    {
        Event::fake();

        $contactMessage = ContactMessage::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
            'expires_at' => $this->faker->dateTimeBetween(),
        ]);

        $contactMessage->save();

        $this
            ->get($contactMessage->generateUrl())
            ->assertForbidden();

        Event::assertNotDispatched(ContactSubmissionConfirmed::class);
    }

    /**
     * Test logic for confirming already confirmed following contact form submission.
     *
     * @return void
     */
    public function testContactEmailConfirmationAlreadyConfirmed()
    {
        Event::fake();

        $contactMessage = ContactMessage::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
            'confirmed_at' => $this->faker->dateTimeBetween(),
        ]);

        $contactMessage->save();

        $this
            ->getJson($contactMessage->generateUrl())
            ->assertConflict();

        Event::assertNotDispatched(ContactSubmissionConfirmed::class);
    }

    /**
     * Tests ContactMessage model is created and not marked as confirmed when confirmation is required.
     *
     * @return void
     */
    public function testContactProcessRequiresConfirmationModelCreated()
    {
        PageSettings::fake('contact', [
            'require_confirmation' => true,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas(ContactMessage::class, [
            ['email', '=', $data['email']],
            ['confirmed_at', '=', null],
        ]);
    }

    /**
     * Tests ContactMessage model is created and marked as confirmed when confirmation isn't required.
     *
     * @return void
     */
    public function testContactProcessModelCreated()
    {
        PageSettings::fake('contact', [
            'require_confirmation' => false,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas(ContactMessage::class, [
            ['email', '=', $data['email']],
            ['confirmed_at', '<>', null],
        ]);
    }

    /**
     * Tests the ContactMessage model is marked as confirmed.
     *
     * @return void
     */
    public function testContactEmailConfirmationModelConfirmed()
    {
        $contactMessage = ContactMessage::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
        ])->useDefaultExpiresAt();

        $contactMessage->save();

        $this
            ->get($contactMessage->generateUrl())
            ->assertSuccessful();

        $this->assertDatabaseHas(ContactMessage::class, [
            ['email', '=', $contactMessage['email']],
            ['confirmed_at', '<>', null],
        ]);
    }
}
