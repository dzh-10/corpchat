<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $app_name;           // اسم التطبيق
    public string $app_tagline;        // الشعار/العبارة
    public string $app_logo;           // رابط الشعار
    public string $app_favicon;        // رابط الفافيكون
    public string $app_language;       // اللغة الافتراضية: ar / fr / en
    public string $app_timezone;       // المنطقة الزمنية
    public bool   $maintenance_mode;   // وضع الصيانة
    public string $maintenance_message; // رسالة الصيانة

    public static function group(): string
    {
        return 'general';
    }
}
