<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::prefix('chat')->middleware(['auth', 'verified'])->group(function () {
    // Inertia
    Route::prefix('inertia')->group(function () {
        Route::get('{roomId?}', [ChatController::class, 'index'])->name('chat.inertia');
        Route::post('message', [ChatController::class, 'store'])->name('chat.message');
    });

    // API
    Route::prefix('api')->group(function () {
        Route::get('', fn () => Inertia::render('chat/ChatApi'))->name('chat.api');
        Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('rooms/{roomId}', [RoomController::class, 'show'])->name('rooms.show');
        Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
