<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat/{roomId?}', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/message', [ChatController::class, 'store'])->name('chat.message');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
