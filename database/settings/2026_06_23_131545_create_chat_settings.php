<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('chat.allow_file_attachments', true);
        $this->migrator->add('chat.max_attachment_size_mb', 10);
        $this->migrator->add('chat.allowed_attachment_types', ['pdf', 'jpg', 'png', 'docx']);
        $this->migrator->add('chat.enable_read_receipts', true);
        $this->migrator->add('chat.enable_typing_indicators', true);
        $this->migrator->add('chat.enable_message_reactions', true);
        $this->migrator->add('chat.enable_group_chats', true);
        $this->migrator->add('chat.max_group_members', 50);
        $this->migrator->add('chat.allow_message_deletion', true);
        $this->migrator->add('chat.allow_message_editing', true);
        $this->migrator->add('chat.message_edit_window_minutes', 15);
        $this->migrator->add('chat.enable_direct_messages', true);
        $this->migrator->add('chat.message_retention_days', 0);
    }
};
