<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\Components\Alerts;
use Tests\Browser\Components\SweetAlerts;
use Tests\Browser\Pages\RegisterPage;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    use DatabaseTruncation;
    use WithFaker;

    /**
     * Tests registering user
     */
    #[Test]
    public function register_user(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->assertGuest()
                ->type('email', $this->faker->email)
                ->type('password', $this->faker->strongPassword)
                ->select('country', 'CAN') // Only certain countries are available in testing
                ->check('terms_conditions')
                ->press('Register')
                ->waitForRoute('user.profile');

            $browser
                ->assertUrlIs(route('user.profile'))
                ->with(new SweetAlerts, function (Browser $browser) {
                    $browser
                        ->assertHasSweetAlerts()
                        ->assertSweetAlertExists(['title' => 'Registered']);
                });
        });
    }

    /**
     * Tests registering user with invalid country
     */
    #[Test]
    public function register_user_invalid_country(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->assertGuest()
                ->type('email', $this->faker->email)
                ->type('password', $this->faker->strongPassword)
                ->select('country', 'XYZ')
                ->check('terms_conditions')
                ->press('Register');

            $browser
                ->assertUrlIs(route('register'))
                ->with(new Alerts, function (Browser $browser) {
                    $browser
                        ->assertHasAlerts()
                        ->assertHasAlert('danger', __('The selected country is invalid.'));
                });
        });
    }
}
