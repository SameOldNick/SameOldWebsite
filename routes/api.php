<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\Auth;
use App\Http\Middleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace(Api::class)->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('refresh', [Api\AuthController::class, 'refresh'])
            ->middleware([Middleware\AuthenticateJWTWithAdapter::adapter(App\Components\LittleJWT\RefreshTokenGuardAdapter::class)]);
    });

    Route::middleware(['auth:jwt', 'can:any-roles-admin'])->group(function () {
        Route::post('/logout', [Auth\LoginController::class, 'apiLogout'])->name('logout');

        Route::get('/countries', [Api\CountriesController::class, 'countries']);
        Route::get('/countries/{country}', [Api\CountriesController::class, 'country']);

        Route::prefix('/user')->group(function () {
            Route::get('/', [Api\UserController::class, 'show'])->withoutMiddleware(['throttle:api'])->name('user');

            Route::post('/avatar', [Api\AvatarController::class, 'uploadAvatar'])->name('avatar.upload');
            Route::delete('/avatar', [Api\AvatarController::class, 'deleteAvatar'])->name('avatar.delete');

            Route::prefix('/notifications')->group(function () {
                Route::get('/', [Api\NotificationsController::class, 'index']);
                Route::get('/read', [Api\NotificationsController::class, 'read']);
                Route::get('/unread', [Api\NotificationsController::class, 'unread']);
                Route::get('/{notification}', [Api\NotificationsController::class, 'show']);
                Route::post('/{notification}/read', [Api\NotificationsController::class, 'markRead']);
                Route::post('/{notification}/unread', [Api\NotificationsController::class, 'markUnread']);
                Route::delete('/{notification}', [Api\NotificationsController::class, 'destroy']);
            });
        });

        Route::prefix('/pages')->group(function () {
            Route::get('/homepage', [Api\Homepage\MetaDataController::class, 'show']);
            Route::post('/homepage', [Api\Homepage\MetaDataController::class, 'update']);

            Route::get('/homepage/social-media', [Api\Homepage\SocialMediaController::class, 'show']);
            Route::post('/homepage/social-media', [Api\Homepage\SocialMediaController::class, 'update']);

            Route::get('/contact', [Api\Contact\MetaDataController::class, 'show']);
            Route::post('/contact', [Api\Contact\MetaDataController::class, 'update']);
        });

        Route::apiResource('images', Api\Blog\ImageController::class)->except(['update']);

        Route::prefix('/blog')->group(function () {
            Route::apiResource('articles', Api\Blog\ArticleController::class);
            Route::apiResource('articles.revisions', Api\Blog\RevisionController::class)->except(['update']);
            Route::apiResource('comments', Api\Blog\CommentController::class)->except(['store']);

            Route::post('/comments/{comment}/approve', [Api\Blog\CommentController::class, 'approve'])->withTrashed();

            Route::post('/articles/restore/{article}', [Api\Blog\ArticleController::class, 'restore'])->withTrashed();
            Route::post('/articles/{article}/revision', [Api\Blog\ArticleController::class, 'revision']);

            Route::get('/articles/{article}/images', [Api\Blog\ArticleImageController::class, 'index']);
            Route::post('/articles/{article}/images/{image}', [Api\Blog\ArticleImageController::class, 'attach']);
            Route::delete('/articles/{article}/images/{image}', [Api\Blog\ArticleImageController::class, 'detach']);

            Route::post('/articles/{article}/images/{image}/main-image', [Api\Blog\ArticleImageController::class, 'mainImage']);
            Route::delete('/articles/{article}/main-image', [Api\Blog\ArticleImageController::class, 'destroyMainImage']);

            Route::get('/articles/{article}/tags', [Api\Blog\TagController::class, 'index']);
            Route::post('/articles/{article}/tags', [Api\Blog\TagController::class, 'attach']);
            Route::put('/articles/{article}/tags', [Api\Blog\TagController::class, 'sync']);
            Route::delete('/articles/{article}/tags', [Api\Blog\TagController::class, 'detach']);
        });

        Route::prefix('/dashboard')->group(function () {
            Route::get('/visitors', [Api\DashboardController::class, 'visitors']);
            Route::get('/links', [Api\DashboardController::class, 'links']);
            Route::get('/browsers', [Api\DashboardController::class, 'browsers']);
        });

        Route::apiResources([
            'projects' => Api\Homepage\ProjectsController::class,
            'tags' => Api\TagController::class,
            'users' => Api\UsersController::class,
            'skills' => Api\Homepage\SkillController::class,
            'technologies' => Api\Homepage\TechnologyController::class,
            'social-media' => Api\Homepage\SocialMediaLinkController::class,
        ]);

        Route::post('/projects/restore/{project}', [Api\Homepage\ProjectsController::class, 'restore'])->withTrashed();
        Route::post('/users/restore/{user}', [Api\UsersController::class, 'restore'])->withTrashed();
    });
});
