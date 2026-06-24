<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ChatSettings extends Settings
{
    public bool   $allow_file_attachments;
    public int    $max_attachment_size_mb;     // default: 10
    public array  $allowed_attachment_types;   // ['pdf','jpg','png','docx']
    public bool   $enable_read_receipts;       // تفعيل علامات القراءة
    public bool   $enable_typing_indicators;   // مؤشر الكتابة
    public bool   $enable_message_reactions;   // تفاعلات الرسائل (Emoji)
    public bool   $enable_group_chats;         
    public int    $max_group_members;          // default: 50
    public bool   $allow_message_deletion;     
    public bool   $allow_message_editing;      
    public int    $message_edit_window_minutes; // نافذة التعديل (دقائق)
    public bool   $enable_direct_messages;     
    public int    $message_retention_days;     // أيام الاحتفاظ بالرسائل (0=forever)

    public static function group(): string
    {
        return 'chat';
    }
}
