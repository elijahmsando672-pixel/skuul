<?php

namespace App\Notifications;

use App\Models\FeeRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeePaymentReminder extends Notification
{
    use Queueable;

    public function __construct(public FeeRecord $feeRecord) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format((float) $this->feeRecord->amount, 2);
        $balance = number_format((float) $this->feeRecord->balance, 2);
        $dueDate = $this->feeRecord->due_date?->format('M d, Y') ?? 'N/A';

        return (new MailMessage)
            ->markdown('notifications.fee-payment-reminder')
            ->subject('Fee Payment Reminder')
            ->greeting('Hello '.($notifiable->name ?? 'there').',')
            ->line('Amount: '.$amount)
            ->line('Balance: '.$balance)
            ->line('Due Date: '.$dueDate)
            ->line('Please arrange payment before the due date.')
            ->action('View Dashboard', route('dashboard'));
    }

    public function shouldSend($notifiable, $channel): bool
    {
        return ! empty($notifiable->email);
    }
}
