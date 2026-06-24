<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ReverbSettings extends Settings
{
    public string $reverb_host;         // reverb-server

    public int $reverb_port;         // 8080

    public string $reverb_scheme;       // http/https

    public string $reverb_app_key;

    public string $reverb_app_secret;   // (encrypted)

    public int $reverb_max_connections; // الحد الأقصى للاتصالات المتزامنة

    public bool $reverb_debug_mode;   // تفعيل وضع التصحيح

    public static function group(): string
    {
        return 'reverb';
    }
}
