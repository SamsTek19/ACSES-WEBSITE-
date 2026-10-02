<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PublicEventController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $events = Cache::remember('api.public.events', now()->addMinutes(5), function (): array {
            $now = now();

            return Event::query()
                ->orderByRaw('CASE WHEN start_at IS NULL THEN 2 WHEN COALESCE(end_at, start_at) >= ? THEN 0 ELSE 1 END', [$now])
                ->orderByRaw('CASE WHEN COALESCE(end_at, start_at) >= ? THEN start_at END ASC', [$now])
                ->orderByRaw('CASE WHEN start_at IS NOT NULL AND COALESCE(end_at, start_at) < ? THEN start_at END DESC', [$now])
                ->get()
                ->map(function (Event $event): array {
                    $start = $event->start_at;
                    $end = $event->end_at;
                    $unscheduled = $start === null;
                    $past = ! $unscheduled && ($end ?? $start)->isPast();
                    $sameDay = $start && $end && $start->isSameDay($end);

                    return [
                        'id' => (string) $event->id,
                        'title' => $event->title,
                        'description' => $event->description ?? '',
                        'date' => $unscheduled
                            ? 'To be announced'
                            : ($sameDay
                            ? $start->format('F j, Y')
                            : ($end ? $start->format('F j, Y') . ' to ' . $end->format('F j, Y') : $start->format('F j, Y'))),
                        'time' => $unscheduled
                            ? ($event->time_label ?: 'To be announced')
                            : ($sameDay
                            ? $start->format('g:i A') . ' - ' . $end->format('g:i A')
                            : $start->format('g:i A')),
                        'location' => $event->location ?? 'Location to be announced',
                        'category' => $unscheduled ? 'Unscheduled' : ($past ? 'Past' : 'Upcoming'),
                        'eventType' => $event->category,
                        'memoriesLink' => $event->memories_link,
                        'link' => $event->cta_url,
                        'image' => $event->banner_url ?? 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774764/IMG_9040.jpg',
                        'imageAlt' => $event->banner_alt ?: $event->title,
                    ];
                })
                ->all();
        });

        return response()->json($events)
            ->header('Cache-Control', 'public, max-age=30, stale-while-revalidate=120')
            ->header('Vary', 'Origin');
    }
}