<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentAnnouncementPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Announcement $announcement)
    {
        $this->announcement->loadMissing('author:user_id,fullname,username,email');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('New ACSES announcement: :title', ['title' => $this->announcement->title]))
            ->view('emails.announcements.student-announcement', [
                'student' => $notifiable,
                'announcement' => $this->announcement,
            ]);
    }
}
