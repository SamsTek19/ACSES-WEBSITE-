<?php

namespace App\Services\Student;

use App\Models\Announcement;
use App\Models\CourseRegistration;
use App\Models\Due;
use App\Models\Event;
use App\Models\AcademicTimelineEntry;
use App\Models\SupportResource;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudentDashboardService
{
    public function heroData(User $user): array
    {
        $firstName = Str::of($user->fullname ?? $user->username ?? 'Student')
            ->trim()
            ->before(' ')
            ->ucfirst();

        $username = $user->username ?: null;
        $displayName = $username ?: $firstName;

        $greeting = match (true) {
            Carbon::now()->isBetween(Carbon::today()->setTime(5, 0), Carbon::today()->setTime(12, 0)) => 'Good morning',
            Carbon::now()->isBetween(Carbon::today()->setTime(12, 0), Carbon::today()->setTime(17, 0)) => 'Good afternoon',
            default => 'Good evening',
        };

        $chips = array_filter([
            $user->class,
            $user->year ? 'Year ' . $user->year : null,
            'Stay informed & connected',
        ]);

        return [
            'greeting' => $greeting,
            // Keep "first_name" for backwards compatibility but now back it with the display name
            'first_name' => (string) $displayName,
            'display_name' => (string) $displayName,
            'username' => (string) ($username ?? $displayName),
            'message' => 'Stay on top of dues, course registration, and departmental updates in one place.',
            'chips' => $chips,
        ];
    }

    public function quickActions(User $user): array
    {
        $cacheKey = "student_actions_meta_{$user->id}";

        return \Illuminate\Support\Facades\Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($user) {
                return [
                    $this->outstandingDuesAction($user),
                    $this->announcementsAction($user),
                    $this->supportResourcesActionCard($user),
                ];
            }
        );
    }

    public function securityTips(): Collection
    {
        $tips = [
            ['title' => 'Enable multi-factor authentication', 'excerpt' => 'Add a secondary verification step on your critical accounts to block credential stuffing attacks.', 'category' => 'Security', 'published' => 'Just now'],
            ['title' => 'Keep software patched', 'excerpt' => 'Install the latest OS, browser, and antivirus updates weekly to close known vulnerabilities.', 'category' => 'Security', 'published' => '5 mins ago'],
            ['title' => 'Use password managers', 'excerpt' => 'Generate unique, complex passwords for every site instead of reusing memorable phrases.', 'category' => 'Best practice', 'published' => '10 mins ago'],
            ['title' => 'Beware of phishing links', 'excerpt' => 'Hover over email links to inspect the real destination before clicking or entering credentials.', 'category' => 'Awareness', 'published' => 'Today'],
            ['title' => 'Lock devices automatically', 'excerpt' => 'Set laptops and phones to auto-lock after a short period of inactivity to prevent shoulder surfing.', 'category' => 'Security', 'published' => 'Today'],
            ['title' => 'Separate work and personal accounts', 'excerpt' => 'Use dedicated profiles for school systems to contain breaches and simplify audit trails.', 'category' => 'Productivity', 'published' => 'Today'],
            ['title' => 'Review app permissions', 'excerpt' => 'Audit mobile apps each month and revoke access to contacts, camera, or location when unnecessary.', 'category' => 'Privacy', 'published' => '1 hour ago'],
            ['title' => 'Secure home Wi-Fi', 'excerpt' => 'Change default router passwords and enable WPA3 encryption to keep intruders off your network.', 'category' => 'Security', 'published' => '1 hour ago'],
            ['title' => 'Back up critical files', 'excerpt' => 'Maintain an encrypted cloud or external drive backup to recover quickly from ransomware.', 'category' => 'Resilience', 'published' => '1 hour ago'],
            ['title' => 'Use HTTPS everywhere', 'excerpt' => 'Verify the browser lock icon before transmitting credentials or personal information.', 'category' => 'Awareness', 'published' => 'Today'],
            ['title' => 'Enable login alerts', 'excerpt' => 'Switch on sign-in notifications so you know immediately when unfamiliar devices access your account.', 'category' => 'Security', 'published' => 'Today'],
            ['title' => 'Log out of shared systems', 'excerpt' => 'Always sign out of lab computers and clear the browser cache after each session.', 'category' => 'Best practice', 'published' => '2 hours ago'],
            ['title' => 'Encrypt portable drives', 'excerpt' => 'Protect USB sticks with encryption to prevent data exposure if they are misplaced.', 'category' => 'Security', 'published' => '2 hours ago'],
            ['title' => 'Verify downloads', 'excerpt' => 'Download installers only from official vendor portals to avoid bundled malware.', 'category' => 'Awareness', 'published' => 'Today'],
            ['title' => 'Use secure messaging', 'excerpt' => 'Discuss sensitive topics on end-to-end encrypted chat platforms instead of plain SMS.', 'category' => 'Privacy', 'published' => 'Today'],
            ['title' => 'Check account recovery info', 'excerpt' => 'Update backup emails and phone numbers so you can regain access if credentials change.', 'category' => 'Account hygiene', 'published' => '3 hours ago'],
            ['title' => 'Monitor data breaches', 'excerpt' => 'Subscribe to breach alert services and rotate passwords if your email appears in leaks.', 'category' => 'Security', 'published' => '3 hours ago'],
            ['title' => 'Use guest networks for visitors', 'excerpt' => 'Keep friends on a segregated Wi-Fi SSID to protect smart devices on your main network.', 'category' => 'Network', 'published' => '3 hours ago'],
            ['title' => 'Disable unused browser extensions', 'excerpt' => 'Remove add-ons you no longer trust to reduce attack surface and improve performance.', 'category' => 'Productivity', 'published' => 'Today'],
            ['title' => 'Scrub metadata before sharing', 'excerpt' => 'Strip GPS and author data from documents or photos shared publicly to avoid oversharing.', 'category' => 'Privacy', 'published' => 'Today'],
        ];

        return collect($tips)->map(fn (array $tip) => [
            'title' => $tip['title'],
            'excerpt' => $tip['excerpt'],
            'published' => $tip['published'],
            'category' => $tip['category'],
        ])->values();
    }

    public function calendarMatrix(): Collection
    {
        $today = Carbon::today();
        $dateKeyStr = $today->toDateString();

        return \Illuminate\Support\Facades\Cache::remember(
            "student_dashboard_calendar_matrix_{$dateKeyStr}",
            now()->addMinutes(60),
            function () use ($today) {
                $startOfMonth = $today->copy()->startOfMonth();
                $endOfMonth = $today->copy()->endOfMonth();

                $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
                $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

                $eventWindow = [$calendarStart->copy()->startOfDay(), $calendarEnd->copy()->endOfDay()];

                $calendarEvents = Event::query()
                    ->whereBetween('start_at', $eventWindow)
                    ->orderBy('start_at')
                    ->get();

                $calendarEventsByDate = $calendarEvents->groupBy(fn (Event $event) => optional($event->start_at)->toDateString());

                $timelineEntries = AcademicTimelineEntry::query()
                    ->published()
                    ->whereBetween('starts_at', $eventWindow)
                    ->orderBy('starts_at')
                    ->get();

                $timelineEntriesByDate = $timelineEntries->groupBy(fn (AcademicTimelineEntry $entry) => optional($entry->starts_at)->toDateString());
                $todayStart = $today->copy()->startOfDay();

                $days = collect(CarbonPeriod::create($calendarStart, $calendarEnd))->map(function (Carbon $date) use ($calendarEventsByDate, $timelineEntriesByDate, $startOfMonth, $endOfMonth, $today, $todayStart) {
                    $dateKey = $date->toDateString();

                    $eventsForDay = collect($calendarEventsByDate->get($dateKey, []))
                        ->map(function (Event $event) use ($todayStart) {
                            $ctaUrl = $event->cta_url ?: route('student.events.index');
                            return [
                                'id' => $event->id,
                                'title' => $event->title,
                                'time' => $event->start_at?->format('g:i A') ?? 'All day',
                                'location' => $event->location,
                                'is_upcoming' => $event->start_at ? $event->start_at->greaterThanOrEqualTo($todayStart) : false,
                                'is_timeline' => false,
                                'cta_url' => $ctaUrl,
                                'is_external' => $event->cta_url ? Str::startsWith($event->cta_url, ['http://', 'https://']) : false,
                                'description' => $event->description ? Str::limit(strip_tags($event->description), 160) : null,
                                'category' => Str::headline($event->category ?? 'Event'),
                            ];
                        });

                    $timelineForDay = collect($timelineEntriesByDate->get($dateKey, []))
                        ->map(function (AcademicTimelineEntry $entry) use ($todayStart) {
                            return [
                                'title' => $entry->title,
                                'time' => $entry->starts_at?->format('g:i A') ?? 'All day',
                                'location' => $entry->summary,
                                'is_upcoming' => $entry->starts_at ? $entry->starts_at->greaterThanOrEqualTo($todayStart) : false,
                                'is_timeline' => true,
                                'cta_url' => $entry->cta_url,
                                'is_external' => $entry->cta_url ? Str::startsWith($entry->cta_url, ['http://', 'https://']) : false,
                                'description' => $entry->description ? Str::limit(strip_tags($entry->description), 160) : ($entry->summary ? Str::limit(strip_tags($entry->summary), 160) : null),
                                'category' => 'Academic milestone',
                                'cta_label' => $entry->cta_label,
                            ];
                        });

                    $combinedEvents = $eventsForDay->merge($timelineForDay);

                    $isCurrentMonth = $date->greaterThanOrEqualTo($startOfMonth) && $date->lessThanOrEqualTo($endOfMonth);

                    return [
                        'date_iso' => $date->toDateString(),
                        'weekday' => $date->isoFormat('ddd'),
                        'day' => $date->format('j'),
                        'month' => $date->format('M'),
                        'is_current_month' => $isCurrentMonth,
                        'is_today' => $date->isSameDay($today),
                        'has_upcoming' => $combinedEvents->contains(function (array $event) {
                            return ! empty($event['is_upcoming']);
                        }),
                        'events' => $combinedEvents,
                    ];
                });

                return $days->chunk(7)
                    ->map(fn (Collection $week) => $week->values())
                    ->values();
            }
        );
    }

    public function upcomingEvents(): Collection
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'student_dashboard_upcoming_events',
            now()->addMinutes(60),
            function () {
                return Event::query()
                    ->upcoming()
                    ->limit(4)
                    ->get()
                    ->map(function (Event $event) {
                        $ctaUrl = $event->cta_url ?: route('student.events.index');
                        return [
                            'title' => $event->title,
                            'datetime' => optional($event->start_at)->format('M j · g:i A'),
                            'location' => $event->location,
                            'banner_url' => $event->banner_url,
                            'banner_alt' => $event->banner_alt,
                            'cta_url' => $ctaUrl,
                            'is_external' => $event->cta_url ? Str::startsWith($event->cta_url, ['http://', 'https://']) : false,
                            'category' => Str::headline($event->category ?? 'Event'),
                            'start_at' => $event->start_at,
                            'month_label' => optional($event->start_at)->format('M'),
                            'day_label' => optional($event->start_at)->format('d'),
                        ];
                    });
            }
        );
    }

    public function supportResources(User $student = null): Collection
    {
        $class = $student?->class ?: null;
        $year = $student?->year !== null ? (string) $student->year : null;

        $resources = SupportResource::query()
            ->forAudience($class, $year)
            ->ordered()
            ->limit(6)
            ->get()
            ->map(function (SupportResource $resource) {
                $isFile = $resource->is_file;
                $ctaLabel = $resource->cta_label ?: ($isFile ? __('Download resource') : __('Open resource'));

                $url = $isFile
                    ? $resource->download_url
                    : ($resource->cta_url ?? '#');

                $openInNewTab = $isFile || ($url && Str::startsWith($url, ['http://', 'https://']));

                return [
                    'title' => $resource->title,
                    'description' => $resource->description,
                    'cta_label' => $ctaLabel,
                    'cta_url' => $url,
                    'open_in_new_tab' => $openInNewTab,
                    'badge_label' => Str::headline($resource->content_type ?? 'Resource'),
                ];
            })->values();

        if ($resources->isNotEmpty()) {
            return $resources;
        }

        return collect([
            [
                'title' => 'Student services hotline',
                'description' => 'Need quick help? Call the ACSES support team between 08:00–20:00 GMT for account or portal assistance.',
                'cta_label' => 'Call 055 935 9824',
                'cta_url' => 'tel:+233559359824',
                'badge_label' => 'Hotline',
            ],
            [
                'title' => 'Email support desk',
                'description' => 'Share detailed issues, screenshots, or requests and the team will reply by the next business day.',
                'cta_label' => 'acsesrepos@gmail.com',
                'cta_url' => 'mailto:acsesrepos@gmail.com',
                'badge_label' => 'Email',
            ],
            [
                'title' => 'Knowledge base',
                'description' => 'Browse how-to guides for dues payments, course registration, and account security.',
                'cta_label' => 'View articles',
                'cta_url' => '#',
                'badge_label' => 'Guide',
            ],
        ]);
    }

    protected function outstandingDuesAction(User $user): array
    {
        $outstandingDues = Due::query()
            ->outstanding()
            ->where('student_id', $user->getAuthIdentifier())
            ->get();

        $totalOwing = $outstandingDues->sum('amount');
        $nextDueDate = $outstandingDues->min('due_date');

        $state = 'All clear';
        $summary = 'No outstanding dues at the moment.';

        if ($totalOwing > 0) {
            $dueDate = $nextDueDate ? Carbon::parse($nextDueDate) : null;

            if ($dueDate && $dueDate->isPast()) {
                $state = 'Overdue';
            } elseif ($dueDate && $dueDate->isBefore(Carbon::now()->addDays(7))) {
                $state = 'Due soon';
            } else {
                $state = 'Active';
            }

            $summary = sprintf(
                'You have %d outstanding due%s awaiting payment%s',
                $outstandingDues->count(),
                $outstandingDues->count() === 1 ? '' : 's',
                $dueDate ? ' (next due ' . $dueDate->format('M j') . ')' : ''
            );
        }

        return [
            'label' => 'Outstanding dues',
            'summary' => $summary,
            'value' => 'GHS ' . number_format((float) $totalOwing, 2),
            'state' => $state,
            'cta' => 'Review dues',
            'cta_url' => route('student.dues.index'),
            'icon_svg' => '<path d="M4 2h16v20l-4-2-4 2-4-2-4 2z" /><path d="M16 6H8" /><path d="M16 10H8" /><path d="M10 14H8" />',
        ];
    }

    protected function announcementsAction(User $user): array
    {
        $baseQuery = Announcement::query()
            ->published()
            ->forStudent($user);

        $total = $baseQuery->count();
        $recentCount = (clone $baseQuery)
            ->where('published_at', '>=', Carbon::now()->subDays(7))
            ->count();

        $state = $recentCount > 0 ? 'New this week' : ($total > 0 ? 'All caught up' : 'No updates');

        if ($recentCount > 0) {
            $summary = sprintf(
                'You have %d announcement%s published in the last 7 days.',
                $recentCount,
                $recentCount === 1 ? '' : 's'
            );
        } elseif ($total > 0) {
            $summary = 'No new announcements this week. Browse earlier updates.';
        } else {
            $summary = 'Announcements for your programme will appear here once published.';
        }

        return [
            'label' => 'Announcements',
            'summary' => $summary,
            'value' => $total . ' update' . ($total === 1 ? '' : 's'),
            'state' => $state,
            'cta' => 'View announcements',
            'cta_url' => route('student.announcements.index'),
            'icon_svg' => '<path d="M4 11V5a2 2 0 0 1 2-2h1l4-2v18l-4-2H6a2 2 0 0 1-2-2v-2" /><path d="M18 7a3 3 0 0 1 0 6" /><path d="M18 3a7 7 0 0 1 0 14" />',
        ];
    }

    protected function courseRegistrationAction(User $user): array
    {
        $registration = CourseRegistration::query()
            ->where('student_id', $user->getAuthIdentifier())
            ->first();

        $statusMap = [
            'not_started' => ['label' => 'Not started', 'state' => 'Start now', 'summary' => 'Begin your registration to secure your courses.'],
            'in_progress' => ['label' => 'In progress', 'state' => 'Action needed', 'summary' => 'Upload pending documents to complete registration.'],
            'submitted' => ['label' => 'Submitted', 'state' => 'Awaiting review', 'summary' => 'Your registration is under review by coordinators.'],
            'approved' => ['label' => 'Approved', 'state' => 'Completed', 'summary' => 'Registration approved for the semester.'],
        ];

        $statusKey = $registration?->status ?? 'not_started';
        $status = $statusMap[$statusKey] ?? $statusMap['not_started'];
        $progress = $registration?->progress_percent ?? 0;
        $pendingDocs = $registration?->pending_documents ?? 0;

        $summary = $status['summary'];
        if ($pendingDocs > 0 && $statusKey !== 'approved') {
            $summary .= sprintf(' %d document%s pending.', $pendingDocs, $pendingDocs === 1 ? ' is' : 's are');
        }

        return [
            'label' => 'Course registration',
            'summary' => $summary,
            'value' => $progress . '% complete',
            'state' => $status['state'],
            'cta' => $statusKey === 'approved' ? 'View submission' : 'Manage registration',
            'cta_url' => '#',
            'icon_svg' => '<path d="M8 4h8" /><path d="M9 2h6" /><path d="M9 6h6" /><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path d="m9 14 2 2 4-4" />',
        ];
    }

    protected function profileCompletenessAction(User $user): array
    {
        $fields = [
            $user->phone_number,
            $user->department,
            $user->profile_picture,
            $user->pending_email ? null : $user->email,
            $user->latitude,
            $user->longitude,
        ];

        $filled = collect($fields)->filter(fn ($value) => ! empty($value))->count();
        $percent = (int) round(($filled / max(count($fields), 1)) * 100);
        $percent = min($percent, 100);

        $state = $percent >= 80 ? 'Complete' : ($percent >= 60 ? 'Good' : 'Update needed');
        $summary = $percent >= 80
            ? 'Your contact and emergency details look great.'
            : 'Keep contact and emergency details up to date for quick support.';

        return [
            'label' => 'Profile completeness',
            'summary' => $summary,
            'value' => $percent . '%',
            'state' => $state,
            'cta' => 'Update profile',
            'cta_url' => '#',
            'icon_svg' => '<rect width="20" height="14" x="2" y="5" rx="2" /><circle cx="8" cy="11" r="2" /><path d="M14 14h4" /><path d="M14 11h4" /><path d="M6 16s1-2 4-2 4 2 4 2" />',
        ];
    }

    protected function supportResourcesActionCard(User $user): array
    {
        $resources = $this->supportResources($user);
        $count = $resources->count();

        $state = $count > 0 ? 'Ready to explore' : 'Coming soon';
        $summary = $count > 0
            ? 'Hand-picked guides and files to support your studies.'
            : 'Support materials for your programme will appear here.';

        return [
            'label' => 'Support resources',
            'summary' => $summary,
            'value' => $count . ' resource' . ($count === 1 ? '' : 's'),
            'state' => $state,
            'cta' => 'Open resources',
            'cta_url' => route('student.resources.index'),
            'icon_svg' => '<path d="M6 3h11a1 1 0 0 1 1 1v14l-3-2-3 2-3-2-3 2V4a1 1 0 0 1 1-1z" /><path d="M9 7h6" /><path d="M9 11h4" />',
        ];
    }

    public function academicTimeline(?string $academicYear = null): Collection
    {
        $cacheKey = 'student_dashboard_timeline_' . ($academicYear ?: 'current');
        
        return \Illuminate\Support\Facades\Cache::remember(
            $cacheKey,
            now()->addMinutes(60),
            function () use ($academicYear) {
                return AcademicTimelineEntry::query()
                    ->published()
                    ->when($academicYear, static function ($query) use ($academicYear): void {
                        $query->where('academic_year', $academicYear);
                    })
                    ->orderBy('starts_at')
                    ->orderBy('id')
                    ->limit(12)
                    ->get()
                    ->map(function (AcademicTimelineEntry $entry) {
                        $startsAt = $entry->starts_at instanceof \Carbon\Carbon
                            ? $entry->starts_at
                            : ($entry->starts_at ? \Carbon\Carbon::parse($entry->starts_at) : null);

                        return [
                            'title' => $entry->title,
                            'date_label' => $startsAt ? $startsAt->format('M j, Y') : null,
                            'month_label' => $startsAt ? $startsAt->format('M') : null,
                            'day_label' => $startsAt ? $startsAt->format('d') : null,
                            'year_label' => $startsAt ? $startsAt->format('Y') : null,
                            'academic_year' => $entry->academic_year,
                            'is_past' => $entry->isPast(),
                        ];
                    });
            }
        );
    }
}
