<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Settings\MailSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Webklex\IMAP\Facades\Client;

class FetchInboundEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-inbound-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch inbound emails from Hostinger IMAP and process them into conversations.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Connecting to IMAP...');

        try {
            $mailSettings = app(MailSettings::class);
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

            $inbox = $client->getFolder('INBOX');
            $messages = $inbox->query()->unseen()->get();

            $this->info("Found {$messages->count()} unread messages.");

            foreach ($messages as $mail) {
                DB::transaction(function () use ($mail) {
                    $fromAddress = $mail->getFrom()[0]->mail;
                    $fromName = $mail->getFrom()[0]->personal ?? $fromAddress;
                    $subject = $mail->getSubject()[0] ?? 'No Subject';
                    $body = $mail->getTextBody() ?? $mail->getHTMLBody() ?? '';
                    $messageId = $mail->getMessageId() ?? null;

                    // Attempt to find In-Reply-To header
                    $inReplyTo = $mail->getInReplyTo() ?? null;

                    // Match conversation via In-Reply-To or From Email
                    $conversation = null;

                    if ($inReplyTo) {
                        $parentMessage = Message::where('message_id_header', $inReplyTo)->first();
                        if ($parentMessage) {
                            $conversation = $parentMessage->conversation;
                        }
                    }

                    if (! $conversation) {
                        $conversation = Conversation::where('external_contact_email', $fromAddress)
                            ->where('type', 'external_email')
                            ->first();
                    }

                    // Create new conversation if none exists
                    if (! $conversation) {
                        $conversation = Conversation::create([
                            'type' => 'external_email',
                            'external_contact_email' => $fromAddress,
                            'external_contact_name' => $fromName,
                            'subject' => $subject,
                        ]);
                    }

                    // Save as new Message
                    $chatMessage = new Message([
                        'conversation_id' => $conversation->id,
                        'sender_id' => null, // External sender
                        'sender_email' => $fromAddress,
                        'sender_name' => $fromName,
                        'body' => $body,
                        'type' => 'inbound_email',
                        'status' => 'delivered',
                        'message_id_header' => $messageId,
                    ]);

                    $chatMessage->save();

                    // Mark as SEEN
                    $mail->setFlag(['Seen']);

                    // Broadcast the Reverb event
                    broadcast(new MessageSent($chatMessage))->toOthers();

                    $this->info("Processed message from {$fromAddress}");
                });
            }

            $client->disconnect();

        } catch (\Exception $e) {
            $this->error('IMAP Error: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Completed fetching emails.');

        return self::SUCCESS;
    }
}
