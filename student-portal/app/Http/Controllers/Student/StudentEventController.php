<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class StudentEventController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $activeCategory = trim((string) $request->query('category'));

        $eventsCollection = $this->loadUpcomingEvents();

        $events = $eventsCollection
            ->filter(function (array $event) use ($search, $activeCategory) {
                $matchesCategory = $activeCategory === '' || $event['category_slug'] === $activeCategory;
                $matchesSearch = $search === ''
                    || Str::contains(Str::lower($event['title']), Str::lower($search))
                    || Str::contains(Str::lower($event['location'] ?? ''), Str::lower($search));

                return $matchesCategory && $matchesSearch;
            })
            ->values();

        $categories = $eventsCollection
            ->pluck('category', 'category_slug')
            ->unique()
            ->map(fn ($label, $slug) => [
                'slug' => $slug,
                'label' => $label,
            ])
            ->sortBy('label')
            ->values();

        return view('dashboards.student.events.index', [
            'title' => 'Upcoming events',
            'events' => $events,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'search' => $search,
        ]);
    }

    private function loadUpcomingEvents(): Collection
    {
        return Event::query()
            ->upcoming()
            ->limit(18)
            ->get()
            ->map(function (Event $event) {
                $category = $event->category ?: 'General';

                $startAt = $event->start_at ? $event->start_at->copy() : null;
                $endAt = $event->end_at
                    ? $event->end_at->copy()
                    : ($startAt ? $startAt->copy()->addHour() : null);

                $startUtc = $startAt ? $startAt->copy()->utc() : null;
                $endUtc = $endAt ? $endAt->copy()->utc() : null;

                $googleDates = $startUtc ? $startUtc->format('Ymd\THis\Z') : null;
                $googleDateRange = $googleDates;
                if ($googleDates && $endUtc) {
                    $googleDateRange .= '/' . $endUtc->format('Ymd\THis\Z');
                }

                $googleUrl = $googleDateRange
                    ? 'https://calendar.google.com/calendar/render?action=TEMPLATE'
                        . '&text=' . urlencode($event->title)
                        . '&dates=' . $googleDateRange
                        . ($event->location ? '&location=' . urlencode($event->location) : '')
                        . ($event->description ? '&details=' . urlencode(strip_tags($event->description)) : '')
                    : null;

                $outlookUrl = $startUtc
                    ? 'https://outlook.office.com/calendar/0/deeplink/compose?path=/calendar/action/compose'
                        . '&rru=addevent'
                        . '&subject=' . urlencode($event->title)
                        . '&startdt=' . $startUtc->format('Y-m-d\TH:i:s\Z')
                        . ($endUtc ? '&enddt=' . $endUtc->format('Y-m-d\TH:i:s\Z') : '')
                        . ($event->location ? '&location=' . urlencode($event->location) : '')
                        . ($event->description ? '&body=' . urlencode(strip_tags($event->description)) : '')
                    : null;

                $icsUrl = route('student.events.ics', $event);
                $webcalUrl = preg_replace('#^https?#', 'webcal', $icsUrl);

                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'location' => $event->location,
                    'category' => Str::headline($category),
                    'category_slug' => Str::slug($category),
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'cta_url' => $event->cta_url,
                    'banner_path' => $event->banner_path,
                    'banner_url' => $event->banner_url,
                    'banner_alt' => $event->banner_alt,
                    'calendar_links' => [
                        'ics' => $icsUrl,
                        'webcal' => $webcalUrl,
                        'google' => $googleUrl,
                        'outlook' => $outlookUrl,
                    ],
                ];
            });
    }

    public function ics(Event $event): Response
    {
        $now = Carbon::now()->utc()->format('Ymd\THis\Z');
        $start = optional($event->start_at)?->copy()->utc()->format('Ymd\THis\Z');
        $end = optional($event->end_at ?: optional($event->start_at)?->copy()->addHour())?->copy()->utc()->format('Ymd\THis\Z');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//ACSES//Student Portal//EN',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:acses-event-' . $event->id . '@' . parse_url(config('app.url'), PHP_URL_HOST),
            'DTSTAMP:' . $now,
            $start ? 'DTSTART:' . $start : null,
            $end ? 'DTEND:' . $end : null,
            'SUMMARY:' . addcslashes($event->title, ",;"),
            $event->location ? 'LOCATION:' . addcslashes($event->location, ",;") : null,
            $event->description ? 'DESCRIPTION:' . addcslashes(strip_tags($event->description), ",;") : null,
            $event->cta_url ? 'URL;VALUE=URI:' . $event->cta_url : null,
            'BEGIN:VALARM',
            'ACTION:DISPLAY',
            'DESCRIPTION:Reminder',
            'TRIGGER:-PT15M',
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        $ics = implode("\r\n", array_filter($lines)) . "\r\n";

        $filename = Str::slug($event->title ?: 'event') . '.ics';

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
