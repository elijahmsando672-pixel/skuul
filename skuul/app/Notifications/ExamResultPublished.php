<?php

namespace App\Notifications;

use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamResultPublished extends Notification
{
    use Queueable;

    public function __construct(public Exam $exam, public ExamResult $result) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $marks = number_format((float) $this->result->marks_obtained, 2);
        $totalMarks = number_format((float) ($this->exam->total_marks ?? 0), 2);
        $grade = $this->result->grade ?? 'N/A';

        return (new MailMessage)
            ->markdown('notifications.exam-result-published')
            ->subject('Exam Results Published')
            ->greeting('Hello '.($notifiable->name ?? 'there').',')
            ->line('Exam: '.$this->exam->name)
            ->line('Marks: '.$marks.' / '.$totalMarks)
            ->line('Grade: '.$grade)
            ->line('Your exam results are now available.')
            ->action('View Exam Results', route('student.exams.index'));
    }

    public function shouldSend($notifiable, $channel): bool
    {
        return ! empty($notifiable->email);
    }
}
