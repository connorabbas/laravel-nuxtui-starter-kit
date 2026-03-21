<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\Examples\UserDirectoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Index');
})->name('index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/examples/paginator/users', [UserDirectoryController::class, 'paginator'])
        ->name('examples.paginator.users');
    Route::get('/examples/table/users', [UserDirectoryController::class, 'table'])
        ->name('examples.table.users');

    Route::redirect('/settings', '/settings/profile')->name('settings');

    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings/password', [PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('/settings/password', [PasswordController::class, 'update'])->name('settings.password.update');

    Route::get('/settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('/settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');
});
