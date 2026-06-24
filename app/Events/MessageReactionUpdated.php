<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.'.$this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.reaction_updated';
    }

    public function broadcastWith(): array
    {
        // Return unified representation mapped through resource
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'reactions' => $this->message->reactions->map(function ($reaction) {
                    return [
                        'id' => $reaction->id,
                        'user_id' => $reaction->user_id,
                        'user_name' => $reaction->user?->name,
                        'reaction' => $reaction->reaction,
                    ];
                })->toArray(),
            ],
        ];
    }
}
