<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppearanceSettings extends Settings
{
    public string $primary_color;       // hex: #6366f1

    public string $theme_mode;          // 'light','dark','system'

    public string $font_family;         // 'inter','cairo','tajawal'

    public bool $enable_glassmorphism; // تأثير Glassmorphism

    public string $sidebar_style;       // 'compact','full','icon-only'

    public bool $enable_animations;

    public string $chat_bubble_style;   // 'modern','classic','minimal'

    public string $date_format;         // 'd/m/Y' أو 'Y-m-d'

    public string $time_format;         // '12h' أو '24h'

    public static function group(): string
    {
        return 'appearance';
    }
}
