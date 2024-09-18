<?php

namespace App\Providers;

use App\Models\File;
use App\Models\Tag;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this
            ->enforceHttps(true)
            ->configureRateLimiting()
            ->bootRouteBindings()
            ->routes(function () {
                Route::middleware('api')
                    ->prefix('api')
                    ->name('api.')
                    ->group(base_path('routes/api.php'));

                Route::middleware('web')
                    ->prefix('/')
                    ->group(base_path('routes/web.php'));

                Route::middleware('admin')
                    ->prefix('admin')
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));
            });
    }

    /**
     * Enforces HTTPS for URLs
     *
     * @return $this
     */
    protected function enforceHttps(bool $enabled)
    {
        if ($enabled) {
            URL::forceScheme('https');
        }

        return $this;
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return $this
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        return $this;
    }

    /**
     * Configures the route bindings
     *
     * @return $this
     */
    protected function bootRouteBindings()
    {
        Route::bind('tag', fn ($value) => Tag::firstWhere('slug', $value));

        Route::bind('file', function ($value) {
            if (Str::isUuid($value)) {
                $uuid = $value;
                $ext = null;
            } elseif (preg_match('/^([\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12})\.([a-z0-9]+)$/iD', $value, $matches)) {
                $uuid = $matches[1];
                $ext = $matches[2];
            } else {
                throw (new ModelNotFoundException)->setModel(File::class);
            }

            $query = File::whereKey($uuid);

            if (! is_null($ext)) {
                $query = $query->whereRaw('LOWER(`path`) LIKE ?', [sprintf('%%%s', Str::lower($ext))]);
            }

            return $query->firstOrFail();
        });

        return $this;
    }
}
