<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.app_name', 'CorpChat');
        $this->migrator->add('general.app_tagline', 'Enterprise Hybrid Messaging');
        $this->migrator->add('general.app_logo', '');
        $this->migrator->add('general.app_favicon', '');
        $this->migrator->add('general.app_language', 'ar');
        $this->migrator->add('general.app_timezone', 'Africa/Algiers');
        $this->migrator->add('general.maintenance_mode', false);
        $this->migrator->add('general.maintenance_message', 'النظام في وضع الصيانة، يرجى المحاولة لاحقاً.');
    }
};
