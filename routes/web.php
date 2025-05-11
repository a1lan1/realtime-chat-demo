<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::prefix('chat')->middleware(['auth', 'verified'])->group(function () {
    // Inertia
    Route::prefix('inertia')->group(function () {
        Route::get('{roomId?}', [ChatController::class, 'index'])->name('chat.inertia');
        Route::post('message', [ChatController::class, 'store'])->name('chat.message');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
