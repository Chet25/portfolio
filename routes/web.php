<?php

use App\Http\Controllers\EditorUploadController;
use App\Http\Controllers\SwitchAccountController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// --- PUBLIC ROUTES ---

// The Dashboard is now the Home/Landing page
Volt::route('/', 'pages.dashboard')->name('dashboard');

// Projects Showcase
Volt::route('/projects', 'pages.projects')->name('dashboard.projects'); // Keeping name for sidebar compat

// API Playground
Volt::route('/api-playground', 'pages.api-playground')->name('api-playground');


// --- AUTHENTICATED ROUTES ---

Route::middleware(['auth'])->group(function () {
    // Settings Redirect
    Route::redirect('settings', 'settings/profile');

    // Settings Pages
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // My Blogs (Content Management)
    Volt::route('dashboard/my-blogs', 'pages.dashboard.my-blogs.index')->name('dashboard.my-blogs.index');
    Volt::route('dashboard/my-blogs/create', 'pages.dashboard.my-blogs.create')->name('dashboard.my-blogs.create');
    Volt::route('dashboard/my-blogs/{blog}/edit', 'pages.dashboard.my-blogs.edit')->name('dashboard.my-blogs.edit');

    // Editor.js Utilities (Uploads must be auth protected to prevent spam)
    Route::post('/api/editor/upload-image', [EditorUploadController::class, 'uploadImage'])->name('editor.upload-image');
    Route::post('/api/editor/fetch-link', [EditorUploadController::class, 'fetchLink'])->name('editor.fetch-link');

    // Account Switching
    Route::get('/switch-account', [SwitchAccountController::class, "index"])->name("switch_account");
    Route::post('/switch-account', [SwitchAccountController::class, "switch"])->name("switch_account.switch");
    Route::post('/logout-current', [SwitchAccountController::class, "logout"])->name("switch_account.logout");

    Route::get('/auth/add-account', [\App\Http\Controllers\Auth\AddAccountController::class, 'create'])->name('auth.add_account');
    Route::post('/auth/add-account', [\App\Http\Controllers\Auth\AddAccountController::class, 'store'])->name('auth.add_account.store');
});

require __DIR__ . '/auth.php';