<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PendingApprovalReminder extends Notification
{
    use Queueable;

    public function __construct(private readonly array $pending)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: approval masih menunggu tindakan')
            ->view('emails.pending-approval-reminder', [
                'approver' => $notifiable,
                'pending' => $this->pending,
                'approvalUrl' => route('approval.index'),
                'logoUrl' => rtrim(config('app.url'), '/') . '/logo.png',
            ]);
    }
}