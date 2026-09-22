<?php

namespace App\Mail\Student;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangeVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public string $verificationUrl,
        public int $expiresInMinutes = 60
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm your new ACSES email address',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student.email-change-verification',
            with: [
                'student' => $this->student,
                'verificationUrl' => $this->verificationUrl,
                'expiresInMinutes' => $this->expiresInMinutes,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
