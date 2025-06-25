<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaRoleController;
use App\Http\Controllers\SaPermissionController;
use App\Http\Controllers\SaUserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaAuthController;
use App\Http\Controllers\RegistrationController;

Route::get('/', [SaAuthController::class, 'index'])->name('login');
Route::post('login', [SaAuthController::class, 'login']);

Route::get('/register', [RegistrationController::class, 'index'])->name('register');
Route::post('/register', [RegistrationController::class, 'registration']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', SaUserController::class);
    Route::resource('roles', SaRoleController::class);
    Route::resource('permissions', SaPermissionController::class);
    Route::resource('products', ProductController::class);
    Route::post('/logout', [SaAuthController::class, 'logout'])->name('logout');
});
