<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
        'name_fr',
        'color',
        'sort_order',
    ];
}
