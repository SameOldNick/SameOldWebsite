<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::namespace(Admin::class)->middleware(['web', 'auth.mfa', 'verified', 'can:any-roles-admin'])->group(function () {
    /**
     * This controller method is specifically requires signing because it provides the JWTs.
     * Once the JWTs are provided, there's nothing stopping a user from using them.
     */
    Route::middleware(['signed'])->get('/sso/{user:uuid}', [Admin\AdminController::class, 'singleSignOn'])->name('sso');

    Route::get('/', [Admin\AdminController::class, 'app']);
    Route::get('/{slug}', [Admin\AdminController::class, 'app'])->where('slug', '([A-z\d\-\/_.]+)?');
});
