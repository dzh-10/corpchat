<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public bool   $enable_browser_notifications;
    public bool   $enable_email_notifications;
    public bool   $enable_sound_notifications;
    public string $notification_sound;         // 'default','ping','chime'
    public bool   $notify_on_new_message;
    public bool   $notify_on_new_email;
    public bool   $notify_on_mention;          // @mention
    public bool   $notify_on_group_message;
    public int    $notification_digest_hours;  // تجميع الإشعارات كل N ساعة (0=فوري)

    public static function group(): string
    {
        return 'notifications';
    }
}
