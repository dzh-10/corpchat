<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\MailListController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // ── Conversations ──────────────────────────────────────────────────
    Route::get('/conversations',  [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);

    // IMPORTANT: 'badges' must be declared BEFORE {conversation} wildcard
    Route::get('/conversations/badges', [ConversationController::class, 'badges']);

    // Folder + Labels management
    Route::patch('/conversations/{conversation}/folder', [ConversationController::class, 'updateFolder']);
    Route::patch('/conversations/{conversation}/labels', [ConversationController::class, 'updateLabels']);

    // ── Messages ───────────────────────────────────────────────────────
    Route::get('/conversations/{conversation}/messages',  [MessageController::class, 'index']);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']);

    // Mark message as read
    Route::patch(
        '/conversations/{conversation}/messages/{message}/read',
        [MessageController::class, 'markRead']
    );

    // Message reactions
    Route::post(
        '/conversations/{conversation}/messages/{message}/reactions',
        [MessageController::class, 'toggleReaction']
    );

    // ── Employees ──────────────────────────────────────────────────────
    Route::get('/employees/search', [EmployeeController::class, 'search']);

    // ── Labels ─────────────────────────────────────────────────────────
    Route::get('/labels',  [LabelController::class, 'index']);
    Route::post('/labels', [LabelController::class, 'store']);

    // ── Mail Lists ─────────────────────────────────────────────────────
    Route::get('/mail-lists',               [MailListController::class, 'index']);
    Route::post('/mail-lists',              [MailListController::class, 'store']);
    Route::delete('/mail-lists/{mailList}', [MailListController::class, 'destroy']);
});
