<?php

namespace App\Notifications;

use App\Models\BookIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LibraryBookDue extends Notification
{
    use Queueable;

    public function __construct(public BookIssue $bookIssue) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bookTitle = $this->bookIssue->book?->title ?? 'N/A';
        $dueDate = $this->bookIssue->due_date?->format('M d, Y') ?? 'N/A';

        return (new MailMessage)
            ->markdown('notifications.library-book-due')
            ->subject('Library Book Due Soon')
            ->greeting('Hello '.($notifiable->name ?? 'there').',')
            ->line('Book Title: '.$bookTitle)
            ->line('Due Date: '.$dueDate)
            ->line('Please return the book on or before the due date.')
            ->action('View Library', route('student.library.index'));
    }

    public function shouldSend($notifiable, $channel): bool
    {
        return ! empty($notifiable->email);
    }
}
