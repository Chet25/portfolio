<?php

use App\Http\Controllers\Api\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API routes are prefixed with /api and use Sanctum for authentication.
|
*/

// Public routes (rate limited: 60 requests/minute)
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/featured', [BlogController::class, 'featured'])->name('blogs.featured');
    Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');
});

// Authenticated routes (rate limited: 30 requests/minute)
Route::middleware(['auth:sanctum', 'throttle:30,1'])->group(function () {
    Route::get('/user', fn(Request $request) => $request->user())->name('user');
});
