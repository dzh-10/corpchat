<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Jobs\SendOutboundEmailJob;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($validated, $user, $conversation) {
            $isExternal = $conversation->type === 'external_email';

            $message = new Message([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'sender_email' => $user->email,
                'sender_name' => $user->name,
                'body' => $validated['body'],
                'type' => $isExternal ? 'outbound_email' : 'internal',
                'status' => $isExternal ? 'sending' : 'delivered',
            ]);

            $message->save();

            // Broadcast immediately to other users in the channel
            broadcast(new MessageSent($message))->toOthers();

            if ($isExternal) {
                // Workflow B: Dispatch job to send outbound email
                SendOutboundEmailJob::dispatch($message);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Message stored successfully.',
                'data' => $message,
            ], 201);
        });
    }
}
