<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SecuritySettings extends Settings
{
    public int $session_timeout_minutes;    // default: 120

    public bool $force_password_change;      // إلزام تغيير كلمة المرور

    public int $password_expiry_days;       // 0 = لا تنتهي

    public int $max_login_attempts;         // default: 5

    public int $lockout_duration_minutes;   // default: 15

    public bool $enable_two_factor;          // 2FA

    public bool $log_user_activity;          // تسجيل أنشطة المستخدمين

    public bool $restrict_ip_access;         // تقييد الوصول بـ IP

    public array $allowed_ips;                // قائمة IPs المسموح بها

    public bool $require_email_verification;

    public static function group(): string
    {
        return 'security';
    }
}
