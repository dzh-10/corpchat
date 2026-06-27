<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailList extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'emails',
    ];

    protected $casts = [
        'emails' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCountAttribute(): int
    {
        return count($this->emails ?? []);
    }
}
