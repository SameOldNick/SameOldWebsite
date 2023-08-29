<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::namespace(Admin::class)->group(function () {
    Route::middleware(['web', 'signed', 'can:admin'])->get('/sso/{user}', [Admin\AdminController::class, 'singleSignOn'])->name('sso');

    Route::get('/', [Admin\AdminController::class, 'app']);
    Route::get('/{slug}', [Admin\AdminController::class, 'app'])->where('slug', '([A-z\d\-\/_.]+)?');
});
