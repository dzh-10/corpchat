<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('security.session_timeout_minutes', 120);
        $this->migrator->add('security.force_password_change', false);
        $this->migrator->add('security.password_expiry_days', 0);
        $this->migrator->add('security.max_login_attempts', 5);
        $this->migrator->add('security.lockout_duration_minutes', 15);
        $this->migrator->add('security.enable_two_factor', false);
        $this->migrator->add('security.log_user_activity', true);
        $this->migrator->add('security.restrict_ip_access', false);
        $this->migrator->add('security.allowed_ips', []);
        $this->migrator->add('security.require_email_verification', false);
    }
};
