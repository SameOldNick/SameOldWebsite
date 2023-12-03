<?php

use App\Http\Controllers;
use App\Http\Middleware;
use Illuminate\Routing\Middleware as LaravelMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);
Route::oauth();

Route::namespace(Controllers\Main::class)->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/avatar', 'HomeController@avatar')->name('home.avatar');

    Route::get('/contact', 'ContactController@show')->name('contact');
    Route::post('/contact', 'ContactController@process')->name('contact.process');
    Route::get('/contact/confirm/{pendingMessage}', 'ContactController@confirm')->name('contact.confirm')->middleware(['signed']);

    Route::view('/terms-conditions', 'main.terms-conditions')->name('terms-conditions');
    Route::view('/privacy-policy', 'main.privacy-policy')->name('privacy-policy');

    Route::get('/blog', 'BlogController@index')->name('blog');
    Route::match(['get', 'post'], '/blog/search', 'BlogController@search')->name('blog.search');

    Route::get('/blog/{year}/{month}', 'BlogController@archive')
        ->name('blog.archive')
        ->where('year', '\d{4}')
        ->where('month', '0?[1-9]|1[012]');

    Route::get('/blog/{article:slug}', 'BlogArticleController@single')->name('blog.single')->can('view', 'article');
    Route::get('/blog/{article:slug}/preview', 'BlogArticleController@single')->name('blog.preview')->middleware(LaravelMiddleware\ValidateSignature::class)->withTrashed();
    Route::get('/blog/{article:slug}/{revision}', 'BlogArticleController@singleRevision')->name('blog.single.revision')->middleware(LaravelMiddleware\ValidateSignature::class);

    Route::get('/blog/{article:slug}/comment/{comment}', 'BlogCommentController@show')->name('blog.comment.show')->can('view', 'comment');
    Route::get('/blog/{article:slug}/comment/{comment}/preview', 'BlogCommentController@show')->name('blog.comment.preview')->middleware(LaravelMiddleware\ValidateSignature::class)->withTrashed();

    Route::middleware(Middleware\FileAccess::class)->group(function () {
        Route::get('/files/{file}', 'FileController@retrieve')->name('file');
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('/blog/{article:slug}/comment', 'BlogCommentController@comment')->name('blog.comment');
        Route::post('/blog/{article:slug}/comments/{parent}', 'BlogCommentController@replyTo')->name('blog.comment.reply-to');

        Route::name('user.')->namespace('User')->group(function () {
            Route::get('/user', 'ProfileController@view')->name('profile');
            Route::post('/user', 'ProfileController@update');

            Route::get('/user/password', 'ChangePasswordController@view')->name('change-password');
            Route::post('/user/password', 'ChangePasswordController@update');
        });
    });

    Route::get('/user/{user}/avatar', [Controllers\Main\User\AvatarController::class, 'view'])->name('user.avatar');
});
