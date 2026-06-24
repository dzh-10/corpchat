<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'sender_email' => $this->sender_email,
            'sender_name' => $this->sender_name,
            'body' => $this->body,
            'type' => $this->type,
            'status' => $this->status,
            'attachment_path' => $this->attachment_path ? asset('storage/'.$this->attachment_path) : null,
            'attachment_name' => $this->attachment_name,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'reactions' => $this->reactions ? $this->reactions->map(function ($reaction) {
                return [
                    'id' => $reaction->id,
                    'user_id' => $reaction->user_id,
                    'user_name' => $reaction->user?->name,
                    'reaction' => $reaction->reaction,
                ];
            }) : [],
        ];
    }
}
