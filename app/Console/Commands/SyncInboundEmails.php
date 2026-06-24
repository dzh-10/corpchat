<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Str;

class SyncInboundEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync inbound emails from Hostinger IMAP server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Connecting to IMAP server...");
        
        try {
            $mailSettings = app(\App\Settings\MailSettings::class);
            config([
                'imap.accounts.default.host' => $mailSettings->imap_host,
                'imap.accounts.default.port' => $mailSettings->imap_port,
                'imap.accounts.default.encryption' => $mailSettings->imap_encryption ?: 'ssl',
                'imap.accounts.default.validate_cert' => (bool) $mailSettings->imap_validate_cert,
                'imap.accounts.default.username' => $mailSettings->imap_username,
                'imap.accounts.default.password' => $mailSettings->imap_password,
            ]);

            $client = Client::account('default');
            $client->connect();
            $this->info("Connected successfully.");

            // Get the INBOX folder
            $folder = $client->getFolder('INBOX');
            
            // Get only unseen messages
            $messages = $folder->query()->unseen()->get();
            $this->info("Found " . $messages->count() . " unseen emails.");

            foreach ($messages as $email) {
                $sender = $email->getFrom()[0];
                $senderEmail = $sender->mail;
                $senderName = $sender->personal ?: $sender->mail;
                $subject = $email->getSubject() ?: 'No Subject';
                
                // Extract plain text body or HTML fallback
                $body = $email->getTextBody() ?: $email->getHTMLBody(true) ?: 'Empty message';

                $this->info("Processing email from: {$senderEmail} - Subject: {$subject}");

                // 1. Find or create conversation by contact email
                $conversation = Conversation::where('type', 'external_email')
                    ->where('external_contact_email', $senderEmail)
                    ->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'uuid' => (string) Str::uuid(),
                        'type' => 'external_email',
                        'external_contact_email' => $senderEmail,
                        'external_contact_name' => $senderName,
                        'subject' => $subject,
                    ]);
                }

                // 2. Save the message as inbound_email
                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => null, // null means external/client
                    'sender_email' => $senderEmail,
                    'sender_name' => $senderName,
                    'body' => $body,
                    'type' => 'inbound_email',
                    'status' => 'delivered',
                    'message_id_header' => $email->getMessageId(),
                ]);

                // Update conversation timestamp to bubble it up
                $conversation->touch();

                // 3. Broadcast the message sent event for real-time UI
                broadcast(new MessageSent($message))->toOthers();

                // 4. Mark email as read on server
                $email->setFlag('Seen');
                $this->info("Email synced and marked as read.");
            }
        } catch (\Exception $e) {
            $this->error("Error during email sync: " . $e->getMessage());
            report($e);
            return 1;
        }

        return 0;
    }
}
