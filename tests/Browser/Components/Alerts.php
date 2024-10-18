<?php

namespace Tests\Browser\Components;

use Illuminate\Support\Arr;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Alerts extends BaseComponent
{
    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return '';
    }

    /**
     * Assert that the browser page contains the component.
     */
    public function assert(Browser $browser): void {}

    /**
     * Get the element shortcuts for the component.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [];
    }

    /**
     * Asserts page has alerts
     *
     * @param Browser $browser
     * @return void
     */
    public function assertHasAlerts(Browser $browser)
    {
        $browser->assertPresent('.alert');
    }

    /**
     * Asserts page has alert
     *
     * @param Browser $browser
     * @param string|null $type Type of alert (danger, warning, etc.)
     * @param string|null $message Alert message
     * @return void
     */
    public function assertHasAlert(Browser $browser, ?string $type = null, ?string $message = null)
    {
        if ($type)
            $browser->assertAttributeContains('.alert', 'class', "alert-{$type}");

        if ($message)
            $browser->assertSeeIn('.alert', $message);
    }
}
