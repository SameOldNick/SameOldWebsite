<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class MacrosServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->urlGeneratorMacros();
        $this->strMacros();
        $this->responseMacros();
        $this->databaseMacros();
    }

    protected function urlGeneratorMacros()
    {
        UrlGenerator::macro('hasValidSignatureNoPath', function (Request $request) {
            /**
             * @var UrlGenerator $this
             */
            $parameters = Arr::except($request->query(), 'signature') + $request->route()->parameters();

            ksort($parameters);

            $original = rtrim('?'.Arr::query($parameters), '?');

            $expires = $request->query('expires');
            $key = call_user_func($this->keyResolver);

            return
                Hash::driver('hash')->check($original, (string) $request->query('signature', ''), ['key' => $key]) &&
                ! ($expires && Carbon::now()->getTimestamp() > $expires);
        });

        UrlGenerator::macro('signedRouteNoPath', function ($name, $parameters = [], $expiration = null, $absolute = true) {
            /**
             * @var UrlGenerator $this
             */
            $parameters = $this->formatParameters($parameters);

            if ($expiration) {
                $parameters = $parameters + ['expires' => $this->availableAt($expiration)];
            }

            ksort($parameters);

            $key = call_user_func($this->keyResolver);

            $signature = Hash::driver('hash')->make(rtrim('?'.Arr::query($parameters), '?'), ['key' => $key]);

            return $this->route($name, $parameters + compact('signature'), $absolute);
        });

        UrlGenerator::macro('temporarySignedRouteNoPath', function ($name, $expiration, $parameters = [], $absolute = true) {
            /**
             * @var UrlGenerator $this
             */

            return $this->signedRouteNoPath($name, $parameters, $expiration, $absolute);
        });
    }

    protected function strMacros()
    {
        Str::macro('secureEquals', function ($known, $user) {
            return hash_equals($known, $user);
        });
    }

    protected function responseMacros()
    {
        Response::macro('fromTranslation', function ($key, array $extra = [], $status = 200, array $headers = []) {
            $keys = Str::of($key)->explode('.');

            $responseKey = $keys->slice($keys->count() > 1 ? 1 : 0)->join('.');

            return Response::withMessage(trans($key), $extra + ['response' => $responseKey], $status, $headers);
        });

        Response::macro('withMessage', function ($message, array $extra = [], $status = 200, array $headers = []) {
            return response($extra + ['message' => $message], $status, $headers);
        });
    }

    protected function databaseMacros()
    {
        /**
         * SQLite doesn't support dropping foreign keys.
         * Source: https://github.com/laravel/framework/issues/23461
         */
        Blueprint::macro('dropForeignSafe', function ($args) {
            /**
             * @var Blueprint $this
             */
            if (app('db.connection') instanceof SQLiteConnection) {
                // Do nothing
                /** @see Blueprint::ensureCommandsAreValid */
            } else {
                $this->dropForeign($args);
            }
        });
    }
}
