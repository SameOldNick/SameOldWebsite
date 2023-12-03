<?php

namespace Tests\Feature\Main;

use App\Events\Contact\ContactSubmissionRequiresApproval;
use App\Events\Contact\ContactSubmissionApproved;
use App\Events\Contact\ContactSubmissionConfirmed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Tests\Feature\Traits\DisablesVite;
use Tests\Feature\Traits\ManagesPageSettings;
use Tests\Feature\Traits\CreatesUser;
use App\Mail\ConfirmMessage;
use App\Mail\Contacted;
use App\Mail\ContactedConfirmation;
use App\Models\PendingMessage;
use App\Models\User;
use Illuminate\Support\Facades\Event;

class ContactTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use DisablesVite;
    use ManagesPageSettings;
    use CreatesUser;

    /**
     * Tests the correct event is fired for an approved contact submission.
     *
     * @return void
     */
    public function testContactSubmissionApprovedEventFired() {
        Event::fake();

        $this->pageSetting('contact', [
            'require_confirmation' => false,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true)
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertSuccessful();

        Event::assertDispatched(ContactSubmissionApproved::class);
    }

    /**
     * Tests the correct event is fired for a message that requires confirmation.
     *
     * @return void
     */
    public function testContactSubmissionRequiresConfirmationEventFired() {
        Event::fake();

        $this->pageSetting('contact', [
            'require_confirmation' => true,
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true)
        ];

        $this
            ->assertGuest()
            ->post(route('contact.process'), $data)
            ->assertSuccessful();

        Event::assertDispatched(ContactSubmissionRequiresApproval::class);
    }

    /**
     * Test logic for guest submitting contact form with confirmation requirement
     *
     * @return void
     */
    public function testGuestContactFormSubmissionRequiresConfirmation()
    {
        Mail::fake();

        $this->pageSetting('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'all_users'
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true)
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

        $this->pageSetting('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'all_users'
        ]);

        $data = [
            'name' => $this->user->getDisplayName(),
            'email' => $this->user->email,
            'message' => $this->faker->paragraphs(3, true)
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

        $this->pageSetting('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_users'
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true)
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

        $this->pageSetting('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_users',
            'recipient_email' => $this->faker->email
        ]);

        $data = [
            'name' => $this->user->getDisplayName(),
            'email' => $this->user->email,
            'message' => $this->faker->paragraphs(3, true)
        ];

        $response =
            $this
                ->actingAs($this->user)
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Thank you for your message! You will receive a reply shortly.'));

        Mail::assertSent(ContactedConfirmation::class, fn (ContactedConfirmation $mail) => $mail->hasTo($data['email']));
        Mail::assertSent(Contacted::class, fn (Contacted $mail) => $mail->hasTo($this->pageSetting('contact', 'recipient_email')));
    }

    /**
     * Test logic for guest submitting contact form with unregistered or unverified user confirmation requirement
     *
     * @return void
     */
    public function testGuestContactFormSubmissionRequiresUnregisteredOrUnverifiedUserConfirmation()
    {
        Mail::fake();

        $this->pageSetting('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_unverified_users'
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true)
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

        $this->pageSetting('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_unverified_users'
        ]);

        $user = User::factory()->unverified()->create();

        $data = [
            'name' => $user->getDisplayName(),
            'email' => $user->email,
            'message' => $this->faker->paragraphs(3, true)
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

        $this->pageSetting('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'unregistered_unverified_users',
            'recipient_email' => $this->faker->email
        ]);

        $data = [
            'name' => $this->user->getDisplayName(),
            'email' => $this->user->email,
            'message' => $this->faker->paragraphs(3, true)
        ];

        $response =
            $this
                ->actingAs($this->user)
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Thank you for your message! You will receive a reply shortly.'));

        Mail::assertSent(ContactedConfirmation::class, fn (ContactedConfirmation $mail) => $mail->hasTo($data['email']));
        Mail::assertSent(Contacted::class, fn (Contacted $mail) => $mail->hasTo($this->pageSetting('contact', 'recipient_email')));
    }

    /**
     * Test logic for guest submitting contact form with no confirmation requirement
     *
     * @return void
     */
    public function testGuestContactFormSubmissionNoConfirmationRequired()
    {
        Mail::fake();

        $this->pageSetting('contact', [
            'require_confirmation' => false,
            'recipient_email' => $this->faker->email
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true)
        ];

        $response =
            $this
                ->assertGuest()
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Thank you for your message! You will receive a reply shortly.'));

        Mail::assertSent(ContactedConfirmation::class, fn (ContactedConfirmation $mail) => $mail->hasTo($data['email']));
        Mail::assertSent(Contacted::class, fn (Contacted $mail) => $mail->hasTo($this->pageSetting('contact', 'recipient_email')));
    }

    /**
     * Test logic for authenticated user submitting contact form with no confirmation requirement
     *
     * @return void
     */
    public function testAuthenticatedUserContactFormSubmissionNoConfirmationRequired()
    {
        Mail::fake();

        $this->pageSetting('contact', [
            'require_confirmation' => false,
            'recipient_email' => $this->faker->email
        ]);

        $data = [
            'name' => $this->user->getDisplayName(),
            'email' => $this->user->email,
            'message' => $this->faker->paragraphs(3, true)
        ];

        $response =
            $this
                ->actingAs($this->user)
                ->post(route('contact.process'), $data);

        $response
            ->assertSuccessful()
            ->assertViewHas('success', fn ($success) => $success === __('Thank you for your message! You will receive a reply shortly.'));

        Mail::assertSent(ContactedConfirmation::class, fn (ContactedConfirmation $mail) => $mail->hasTo($data['email']));
        Mail::assertSent(Contacted::class, fn (Contacted $mail) => $mail->hasTo($this->pageSetting('contact', 'recipient_email')));
    }

    /**
     * Test logic for verifying email following contact form submission.
     *
     * @return void
     */
    public function testVerifyEmailFollowingSubmission()
    {
        Mail::fake();

        $this->pageSetting('contact', [
            'require_confirmation' => true,
            'confirmation_required_by' => 'all_users',
            'recipient_email' => $this->faker->email
        ]);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true)
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

        Mail::assertSent(Contacted::class, fn (Contacted $mail) => $mail->hasTo($this->pageSetting('contact', 'recipient_email')));
    }

    /**
     * Test logic for verifying email following contact form submission.
     *
     * @return void
     */
    public function testContactEmailConfirmationEventFired()
    {
        Event::fake();

        $pendingMessage = PendingMessage::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true)
        ])->useDefaultExpiresAt();

        $pendingMessage->save();

        $this
            ->get($pendingMessage->generateUrl())
            ->assertSuccessful();

        Event::assertDispatched(ContactSubmissionConfirmed::class);
        Event::assertDispatched(ContactSubmissionApproved::class);
    }

    /**
     * Test logic for confirming email with expired link following contact form submission.
     *
     * @return void
     */
    public function testContactEmailConfirmationExpired()
    {
        Event::fake();

        $pendingMessage = PendingMessage::make([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraphs(3, true),
            'expires_at' => $this->faker->dateTimeBetween()
        ]);

        $pendingMessage->save();

        $this
            ->get($pendingMessage->generateUrl())
            ->assertForbidden();

        Event::assertNotDispatched(ContactSubmissionConfirmed::class);
        Event::assertNotDispatched(ContactSubmissionApproved::class);
    }
}
