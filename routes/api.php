<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Conversations list and creation
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);

    // Messages list and send
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index']);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']);

    // Message reactions
    Route::post('/conversations/{conversation}/messages/{message}/reactions', [MessageController::class, 'toggleReaction']);

    // Employee search autocomplete
    Route::get('/employees/search', [EmployeeController::class, 'search']);
});
