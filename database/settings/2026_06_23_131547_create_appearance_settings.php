<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('appearance.primary_color', '#378ADD');
        $this->migrator->add('appearance.theme_mode', 'system');
        $this->migrator->add('appearance.font_family', 'inter');
        $this->migrator->add('appearance.enable_glassmorphism', true);
        $this->migrator->add('appearance.sidebar_style', 'full');
        $this->migrator->add('appearance.enable_animations', true);
        $this->migrator->add('appearance.chat_bubble_style', 'modern');
        $this->migrator->add('appearance.date_format', 'Y-m-d');
        $this->migrator->add('appearance.time_format', '24h');
    }
};
