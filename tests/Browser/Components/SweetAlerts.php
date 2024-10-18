<?php

namespace Tests\Browser\Components;

use Illuminate\Support\Arr;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class SweetAlerts extends BaseComponent
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
    public function assert(Browser $browser): void
    {
        $browser->assertScript('window.sweetAlerts === null || Array.isArray(window.sweetAlerts)');
    }

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
     * Asserts page has sweet alerts
     *
     * @return void
     */
    public function assertHasSweetAlerts(Browser $browser, ?int $count = null)
    {
        $browser->assertScript(
            $count ?
                sprintf('window.sweetAlerts && window.sweetAlerts.length === %d', $count) :
                'window.sweetAlerts && window.sweetAlerts.length > 0'
        );
    }

    /**
     * Asserts page has sweet alert with options
     *
     * @return void
     */
    public function assertSweetAlertExists(Browser $browser, array $options)
    {
        // Build conditions to check against SweetAlert options
        $alertConditions = Arr::map($options, fn ($value, $key) => sprintf('alert.%s === %s', $key, json_encode($value)));

        // Create the script for checking SweetAlert existence
        $script = sprintf(
            'window.sweetAlerts && window.sweetAlerts.some(alert => %s)',
            implode(' && ', $alertConditions)
        );

        // Assert the condition in the browser
        $browser->assertScript($script);
    }
}
