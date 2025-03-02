<?php

namespace App\Components\Captcha\Facades;

use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Testing\FakeCaptchaService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void validate(UserResponse $userResponse)
 * @method static CaptchaManager getManager()
 * @method static Driver getDriver(?string $driver = null)
 *
 * @see \App\Components\Captcha\CaptchaService
 */
class Captcha extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'captcha';
    }

    /**
     * Swap the underlying captcha service implementation with a fake.
     *
     * @param  Driver|null  $driver  Driver implementation to use
     * @param  string|null  $driverName  Driver names to use the driver for (wildcard: '*')
     */
    public static function fake(?Driver $driver = null, ?string $driverName = null): void
    {
        $drivers = $driver ? [
            $driverName ?? '*' => $driver,
        ] : [];

        static::fakes($drivers);
    }

    /**
     * Swap the underlying captcha service implementation with a fake.
     *
     * @param  array<string, Driver>  $drivers  Drivers to fake
     */
    public static function fakes(array $drivers): void
    {
        static::swap(new FakeCaptchaService(static::$app, $drivers));
    }
}
