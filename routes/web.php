<?php

use App\Http\Controllers\SwitchAccountController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/switch-account', [SwitchAccountController::class, "index"])->name("switch_account");
    Route::post('/switch-account', [SwitchAccountController::class, "switch"])->name("switch_account.switch");
    Route::post('/logout-current', [SwitchAccountController::class, "logout"])->name("switch_account.logout");

    Route::get('/auth/add-account', [\App\Http\Controllers\Auth\AddAccountController::class, 'create'])->name('auth.add_account');
    Route::post('/auth/add-account', [\App\Http\Controllers\Auth\AddAccountController::class, 'store'])->name('auth.add_account.store');
});

require __DIR__ . '/auth.php';
