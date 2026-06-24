<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\MessageReactionUpdated;
use App\Events\MessageSent;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Jobs\SendOutboundEmailJob;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(Request $request, Conversation $conversation): AnonymousResourceCollection
    {
        $messages = $conversation->messages()
            ->with('reactions.user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Reverse the items so they are returned in chronological order (oldest to newest) for page rendering
        $messages->setCollection($messages->getCollection()->reverse()->values());

        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request, Conversation $conversation): MessageResource
    {
        $validated = $request->validated();
        $user = $request->user();

        return DB::transaction(function () use ($validated, $user, $conversation, $request) {
            $isExternal = $conversation->type === 'external_email';

            // Handle attachment
            $attachmentPath = null;
            $attachmentName = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                $attachmentName = $request->file('attachment')->getClientOriginalName();
            }

            $message = new Message([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'sender_email' => $user->email,
                'sender_name' => $user->name,
                'body' => $validated['body'],
                'type' => $isExternal ? 'outbound_email' : 'internal',
                'status' => $isExternal ? 'sending' : 'delivered',
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            $message->save();

            // Touch conversation to update timestamps
            $conversation->touch();

            // Clear conversations list cache
            Cache::tags(['conversations'])->flush();

            // Load reactions relation (empty for new message)
            $message->load('reactions.user');

            // Broadcast immediately to other users in the channel
            broadcast(new MessageSent($message))->toOthers();

            if ($isExternal) {
                SendOutboundEmailJob::dispatch($message);
            }

            return new MessageResource($message);
        });
    }

    public function toggleReaction(Request $request, Conversation $conversation, Message $message)
    {
        $validated = $request->validate([
            'reaction' => 'required|string|max:10',
        ]);

        $userId = $request->user()->id;

        $existing = $message->reactions()
            ->where('user_id', $userId)
            ->where('reaction', $validated['reaction'])
            ->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
        } else {
            $message->reactions()->create([
                'user_id' => $userId,
                'reaction' => $validated['reaction'],
            ]);
            $action = 'added';
        }

        // Load complete reactions relationship
        $message->load('reactions.user');

        // Broadcast reaction update
        broadcast(new MessageReactionUpdated($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'data' => new MessageResource($message),
        ]);
    }
}
