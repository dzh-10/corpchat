<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mail.mail_mailer', 'smtp');
        $this->migrator->add('mail.mail_host', 'smtp.hostinger.com');
        $this->migrator->add('mail.mail_port', 465);
        $this->migrator->add('mail.mail_encryption', 'ssl');
        $this->migrator->add('mail.mail_username', 'employee@company.com');
        $this->migrator->add('mail.mail_password', 'your_hostinger_password');
        $this->migrator->add('mail.mail_from_address', 'employee@company.com');
        $this->migrator->add('mail.mail_from_name', 'CorpChat');

        $this->migrator->add('mail.imap_host', 'imap.hostinger.com');
        $this->migrator->add('mail.imap_port', 993);
        $this->migrator->add('mail.imap_encryption', 'ssl');
        $this->migrator->add('mail.imap_username', 'employee@company.com');
        $this->migrator->add('mail.imap_password', 'your_hostinger_password');
        $this->migrator->add('mail.imap_validate_cert', true);
        $this->migrator->add('mail.imap_sync_folder', 'INBOX');
        $this->migrator->add('mail.imap_sync_interval', 1);
    }
};
