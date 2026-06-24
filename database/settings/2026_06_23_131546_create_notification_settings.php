<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notifications.enable_browser_notifications', true);
        $this->migrator->add('notifications.enable_email_notifications', true);
        $this->migrator->add('notifications.enable_sound_notifications', true);
        $this->migrator->add('notifications.notification_sound', 'default');
        $this->migrator->add('notifications.notify_on_new_message', true);
        $this->migrator->add('notifications.notify_on_new_email', true);
        $this->migrator->add('notifications.notify_on_mention', true);
        $this->migrator->add('notifications.notify_on_group_message', true);
        $this->migrator->add('notifications.notification_digest_hours', 0);
    }
};
