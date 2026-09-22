<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $token,
        private readonly string $email
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('password.reset', ['token' => $this->token, 'email' => $this->email]);

        return (new MailMessage())
            ->subject(__('Reset your ACSES Portal password'))
            ->view('emails.auth.password_reset', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
            ]);
    }
}
