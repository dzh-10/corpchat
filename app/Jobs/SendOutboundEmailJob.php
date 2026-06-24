<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\MessageSent;
use App\Models\Message;
use App\Settings\MailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message as MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOutboundEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Message $messageModel;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $conversation = $this->messageModel->conversation;

        if (! $conversation || ! $conversation->external_contact_email) {
            $this->messageModel->update(['status' => 'failed']);

            return;
        }

        try {
            $mailSettings = app(MailSettings::class);
            $fromAddress = $mailSettings->mail_from_address;
            $fromName = $mailSettings->mail_from_name;

            $mailer = Mail::build([
                'transport' => 'smtp',
                'host' => $mailSettings->mail_host,
                'port' => $mailSettings->mail_port,
                'encryption' => $mailSettings->mail_encryption ?: null,
                'username' => $mailSettings->mail_username,
                'password' => $mailSettings->mail_password,
                'timeout' => 10,
            ]);

            $mailer->raw($this->messageModel->body, function (MailMessage $mail) use ($conversation, $fromAddress, $fromName) {
                $mail->to($conversation->external_contact_email, $conversation->external_contact_name)
                    ->subject($conversation->subject ?? 'New message from CorpChat')
                    ->from($fromAddress, $fromName);
            });

            // Update status upon successful dispatch
            $this->messageModel->update(['status' => 'sent']);
            broadcast(new MessageSent($this->messageModel));

        } catch (\Exception $e) {
            $this->messageModel->update(['status' => 'failed']);
            broadcast(new MessageSent($this->messageModel));
            report($e);
        }
    }
}
