<?php

namespace App\Notifications;

use App\Enums\SuggestionStatus;
use App\Models\Suggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentSuggestionStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Suggestion $suggestion, private readonly SuggestionStatus $previous)
    {
        $this->suggestion->loadMissing('user:user_id,fullname,username,email');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = SuggestionStatus::tryFrom($this->suggestion->status)?->label() ?? ucfirst($this->suggestion->status ?? 'pending');

        return (new MailMessage())
            ->subject(__('We have updated your suggestion: :subject', ['subject' => $this->suggestion->subject]))
            ->view('emails.suggestions.status-updated', [
                'student' => $notifiable,
                'suggestion' => $this->suggestion,
                'statusLabel' => $statusLabel,
                'previousLabel' => $this->previous->label(),
            ]);
    }
}
