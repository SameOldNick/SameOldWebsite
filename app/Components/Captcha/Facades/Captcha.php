<?php

namespace App\Components\Captcha\Facades;

use App\Components\Captcha\Contracts\Driver;
use App\Components\Captcha\Testing\Driver as TestingDriver;
use App\Components\Captcha\Testing\FakeCaptchaService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void validate(UserResponse $userResponse)
 * @method static CaptchaManager getManager()
 * @method static Driver getDriver(?string $driver)
 * @method static void fake(?Driver $driver)
 * 
 * @see \App\Components\Captcha\CaptchaService
 */
class Captcha extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'captcha';
    }

    public static function fake(?Driver $driver = null): void
    {
        static::swap(new FakeCaptchaService(static::$app, $driver));
    }
}
