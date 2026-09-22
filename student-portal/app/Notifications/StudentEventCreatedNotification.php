<?php

namespace App\Notifications;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentEventCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var array<string, mixed>
     */
    private array $eventData;

    public function __construct(Event $event)
    {
        $this->eventData = [
            'title' => $event->title,
            'description' => $event->description,
            'location' => $event->location,
            'start_at' => optional($event->start_at)->toIso8601String(),
            'end_at' => optional($event->end_at)->toIso8601String(),
            'cta_url' => $event->cta_url,
        ];
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $start = $this->eventData['start_at']
            ? Carbon::parse($this->eventData['start_at'])->timezone(config('app.timezone'))
            : null;

        $end = $this->eventData['end_at']
            ? Carbon::parse($this->eventData['end_at'])->timezone(config('app.timezone'))
            : null;

        return (new MailMessage())
            ->subject(__('New campus event: :title', ['title' => $this->eventData['title']]))
            ->view('emails.events.student-event-created', [
                'student' => $notifiable,
                'event' => $this->eventData,
                'start' => $start,
                'end' => $end,
            ]);
    }
}
