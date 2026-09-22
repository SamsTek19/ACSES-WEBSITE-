<?php

namespace App\Mail\Student;

use App\Models\CourseRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CourseRegistrationStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CourseRegistration $registration,
        public ?string $previousStatus = null
    ) {
        $this->registration->loadMissing('student');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your course registration status was updated',
        );
    }

    public function content(): Content
    {
        $statusLabel = Str::headline($this->registration->status);
        $previousLabel = $this->previousStatus ? Str::headline($this->previousStatus) : null;

        return new Content(
            view: 'emails.student.course-registration-status-updated',
            with: [
                'student' => $this->registration->student,
                'registration' => $this->registration,
                'statusLabel' => $statusLabel,
                'previousStatusLabel' => $previousLabel,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
