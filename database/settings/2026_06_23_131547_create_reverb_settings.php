<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('reverb.reverb_host', 'reverb-server');
        $this->migrator->add('reverb.reverb_port', 8080);
        $this->migrator->add('reverb.reverb_scheme', 'http');
        $this->migrator->add('reverb.reverb_app_key', 'corpchat_key');
        $this->migrator->add('reverb.reverb_app_secret', 'corpchat_secret');
        $this->migrator->add('reverb.reverb_max_connections', 1000);
        $this->migrator->add('reverb.reverb_debug_mode', false);
    }
};
