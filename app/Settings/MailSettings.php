<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings
{
    public string $mail_mailer;        // smtp
    public string $mail_host;          // smtp.hostinger.com
    public int    $mail_port;          // 465
    public string $mail_encryption;    // ssl/tls
    public string $mail_username;      
    public string $mail_password;      // (encrypted)
    public string $mail_from_address;  
    public string $mail_from_name;     

    public string $imap_host;          // imap.hostinger.com
    public int    $imap_port;          // 993
    public string $imap_encryption;    // ssl
    public string $imap_username;      
    public string $imap_password;      // (encrypted)
    public bool   $imap_validate_cert; 
    public string $imap_sync_folder;   // INBOX
    public int    $imap_sync_interval; // sync every N minutes

    public static function group(): string
    {
        return 'mail';
    }
}
