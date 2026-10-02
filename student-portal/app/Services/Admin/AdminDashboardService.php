<?php

namespace App\Services\Admin;

use App\Models\Event;
use App\Models\Suggestion;
use App\Models\SupportResource;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminDashboardService
{
    public function overview(?User $admin): array
    {
        $adminName = $admin?->fullname ?? $admin?->username ?? 'Administrator';
        $greeting = $this->greetingForNow();
        $now = Carbon::now();

        $upcomingEventCount = Event::query()->upcoming()->count();
        $resourcesTotal = SupportResource::query()->count();

        $suggestionsPending = Suggestion::query()
            ->where('status', 'pending')
            ->count();
        $suggestionsResolvedWeek = Suggestion::query()
            ->where('status', 'resolved')
            ->where('handled_at', '>=', $now->copy()->startOfWeek())
            ->count();

        $overviewCards = [
            [
                'label' => 'Upcoming events',
                'value' => number_format($upcomingEventCount),
                'description' => 'Scheduled events ready for the public website.',
                'icon' => 'ri-calendar-event-fill',
                'link' => route('admin.events.index'),
                'cta' => 'Manage events',
            ],
            [
                'label' => 'Learning resources',
                'value' => number_format($resourcesTotal),
                'description' => 'Resources available to the student community.',
                'icon' => 'ri-book-open-fill',
                'link' => route('admin.resources.index'),
                'cta' => 'Manage resources',
            ],
            [
                'label' => 'Suggestions awaiting review',
                'value' => number_format($suggestionsPending),
                'description' => $suggestionsResolvedWeek . ' resolved this week.',
                'icon' => 'ri-chat-smile-3-fill',
                'link' => route('admin.suggestions.index'),
                'cta' => 'Open suggestions',
            ],
        ];

        $upcomingEvents = Event::query()
            ->upcoming()
            ->limit(3)
            ->get()
            ->map(function (Event $event): array {
                return [
                    'title' => $event->title,
                    'schedule' => optional($event->start_at)->isoFormat('MMM D · h:mm A'),
                    'category' => Str::headline($event->category ?? 'General'),
                    'location' => $event->location,
                ];
            });

        $recentSuggestions = Suggestion::query()
            ->with(['user:user_id,fullname,username,email'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function (Suggestion $suggestion): array {
                return [
                    'subject' => $suggestion->subject,
                    'category' => Str::headline($suggestion->category ?? 'General'),
                    'status' => Str::headline($suggestion->status ?? 'Pending'),
                    'submitted_at' => optional($suggestion->created_at)->diffForHumans(),
                    'owner' => $suggestion->user?->fullname
                        ?? $suggestion->user?->username
                        ?? 'Student',
                ];
            });

        return [
            'adminName' => $adminName,
            'hero' => [
                'greeting' => $greeting,
                'message' => 'Manage public content and review community feedback from a single view.',
                'lastUpdated' => $now->isoFormat('MMMM D, YYYY [at] h:mm A'),
            ],
            'overviewCards' => $overviewCards,
            'resourcesTotal' => $resourcesTotal,
            'upcomingEvents' => $upcomingEvents,
            'recentSuggestions' => $recentSuggestions,
        ];
    }

    private function greetingForNow(): string
    {
        $now = Carbon::now();
        $hour = (int) $now->format('H');

        return match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };
    }
}
