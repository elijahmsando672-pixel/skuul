<?php

namespace App\Notifications;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmation extends Notification
{
    use Queueable;

    public function __construct(
        public Student $student,
        public Classroom $classroom,
        public AcademicYear $academicYear,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->student->user?->name ?? $this->student->student_id;

        return (new MailMessage)
            ->markdown('notifications.enrollment-confirmation')
            ->subject('Enrollment Confirmed')
            ->greeting('Hello '.($notifiable->name ?? 'there').',')
            ->line('Student: '.$studentName)
            ->line('Classroom: '.$this->classroom->name)
            ->line('Academic Year: '.$this->academicYear->name)
            ->line('Your enrollment has been confirmed.')
            ->action('View Student Dashboard', route('student.dashboard'));
    }

    public function shouldSend($notifiable, $channel): bool
    {
        return ! empty($notifiable->email);
    }
}
