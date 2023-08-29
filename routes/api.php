<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware;

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
    Route::prefix('/auth')->group(function() {
        Route::post('refresh', [Api\AuthController::class, 'refresh'])
            ->middleware([Middleware\AuthenticateJWTWithAdapter::adapter(\App\Components\LittleJWT\RefreshTokenGuardAdapter::class)]);
    });

    Route::middleware(['auth:jwt', 'can:admin'])->group(function () {
        Route::get('/countries', [Api\CountriesController::class, 'countries']);
        Route::get('/countries/{country}', [Api\CountriesController::class, 'country']);

        Route::get('/user', [Api\UserController::class, 'show'])->withoutMiddleware(['throttle:api']);;

        Route::get('/user/avatar', [Api\AvatarController::class, 'avatar'])->name('avatar');
        Route::post('/user/avatar', [Api\AvatarController::class, 'uploadAvatar'])->name('avatar.upload');
        Route::delete('/user/avatar', [Api\AvatarController::class, 'deleteAvatar'])->name('avatar.delete');

        Route::post('/logout', [Auth\LoginController::class, 'apiLogout'])->name('logout');

        Route::get('/pages/homepage', [Api\Homepage\MetaDataController::class, 'show']);
        Route::post('/pages/homepage', [Api\Homepage\MetaDataController::class, 'update']);

        Route::get('/pages/homepage/social-media', [Api\Homepage\SocialMediaController::class, 'show']);
        Route::post('/pages/homepage/social-media', [Api\Homepage\SocialMediaController::class, 'update']);

        Route::get('/pages/contact', [Api\Contact\MetaDataController::class, 'show']);
        Route::post('/pages/contact', [Api\Contact\MetaDataController::class, 'update']);

        Route::apiResources([
            'projects' => Api\Homepage\ProjectsController::class,
            'tags' => Api\TagController::class,
            'users' => Api\UsersController::class,
            'skills' => Api\Homepage\SkillController::class,
            'technologies' => Api\Homepage\TechnologyController::class,
        ]);

        Route::post('/projects/restore/{project}', [Api\Homepage\ProjectsController::class, 'restore'])->withTrashed();
        Route::post('/users/restore/{user}', [Api\UsersController::class, 'restore'])->withTrashed();

        Route::get('/user/notifications', [Api\NotificationsController::class, 'index']);
        Route::get('/user/notifications/read', [Api\NotificationsController::class, 'read']);
        Route::get('/user/notifications/unread', [Api\NotificationsController::class, 'unread']);
        Route::get('/user/notifications/{notification}', [Api\NotificationsController::class, 'show']);
        Route::post('/user/notifications/{notification}/read', [Api\NotificationsController::class, 'markRead']);
        Route::post('/user/notifications/{notification}/unread', [Api\NotificationsController::class, 'markUnread']);
        Route::delete('/user/notifications/{notification}', [Api\NotificationsController::class, 'destroy']);


    });

    Route::get('/user/{user}/avatar/download', [Api\AvatarController::class, 'downloadAvatar'])->middleware(['signed:t'])->name('avatar.download');

});
