<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Jobs\SendOutboundEmailJob;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $page = (int) $request->get('page', 1);

        // Cache using tags, expires in 10 seconds for high responsiveness
        $conversations = Cache::tags(['conversations'])->remember("list_page_{$page}", 10, function () {
            return Conversation::with(['messages' => function ($q) {
                $q->latest()->limit(1);
            }])
                ->orderBy('updated_at', 'desc')
                ->paginate(20);
        });

        return ConversationResource::collection($conversations);
    }

    public function store(StoreConversationRequest $request)
    {
        $validated = $request->validated();
        $currentUser = $request->user();

        return DB::transaction(function () use ($validated, $currentUser, $request) {
            // Normalize types
            $rawType = $validated['type'];
            $type = ($rawType === 'email' || $rawType === 'external_email') ? 'external_email' : 'internal_chat';

            if ($type === 'external_email') {
                $email = $validated['client_email'] ?? $validated['external_contact_email'];
                $name = $validated['client_name'] ?? $validated['external_contact_name'] ?? $email;

                $conversation = Conversation::where('type', 'external_email')
                    ->where('external_contact_email', $email)
                    ->first();

                if (! $conversation) {
                    $conversation = Conversation::create([
                        'uuid' => (string) Str::uuid(),
                        'type' => 'external_email',
                        'external_contact_email' => $email,
                        'external_contact_name' => $name,
                        'subject' => $validated['subject'] ?: 'No Subject',
                    ]);
                }
            } else {
                $recipientId = $validated['recipient_id'] ?? null;
                if ($recipientId) {
                    $recipient = User::findOrFail($recipientId);
                    $subject = $validated['subject'] ?: 'Chat with '.$recipient->name;
                } else {
                    $subject = $validated['subject'] ?: 'Internal Group';
                }

                $conversation = Conversation::create([
                    'uuid' => (string) Str::uuid(),
                    'type' => 'internal_chat',
                    'subject' => $subject,
                ]);
            }

            // Handle attachment
            $attachmentPath = null;
            $attachmentName = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                $attachmentName = $request->file('attachment')->getClientOriginalName();
            }

            // Create initial message
            $message = new Message([
                'conversation_id' => $conversation->id,
                'sender_id' => $currentUser->id,
                'sender_email' => $currentUser->email,
                'sender_name' => $currentUser->name,
                'body' => $validated['body'],
                'type' => $type === 'external_email' ? 'outbound_email' : 'internal',
                'status' => $type === 'external_email' ? 'sending' : 'delivered',
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            $message->save();

            // Touch conversation to update timestamps
            $conversation->touch();

            // Clear conversations cache
            Cache::tags(['conversations'])->flush();

            // Broadcast message
            broadcast(new MessageSent($message))->toOthers();

            if ($type === 'external_email') {
                SendOutboundEmailJob::dispatch($message);
            }

            return (new ConversationResource($conversation))
                ->additional(['success' => true]);
        });
    }
}
