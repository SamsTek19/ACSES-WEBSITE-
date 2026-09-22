<?php

namespace App\Services\Admin;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\StudentAnnouncementPublishedNotification;
use App\Services\Notification\SmsNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AdminAnnouncementService
{
    public const TYPES = [
        'general' => 'General',
        'security' => 'Security',
        'maintenance' => 'Maintenance',
    ];

    public const PRIORITIES = [
        'high' => 'High',
        'normal' => 'Normal',
        'low' => 'Low',
    ];

    public const TARGET_TYPES = [
        'all' => 'All students',
        'student' => 'Specific students',
        'class' => 'Specific class',
        'year' => 'Specific year group',
        'class_year' => 'Specific class & year',
    ];

    public function __construct(private readonly SmsNotificationService $smsService)
    {
    }

    public function list(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Announcement::query()
            ->with('author:user_id,fullname,username,email')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        if ($search = trim((string) Arr::get($filters, 'search', ''))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($type = Arr::get($filters, 'type')) {
            $query->where('type', $type);
        }

        if ($priority = Arr::get($filters, 'priority')) {
            $query->where('priority', $priority);
        }

        if ($target = Arr::get($filters, 'target_type')) {
            $query->where('target_type', $target);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function targetOptions(): array
    {
        $classOptions = User::query()
            ->where('role', 'student')
            ->whereNotNull('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class')
            ->filter()
            ->map(fn ($value) => trim((string) $value))
            ->merge(StudentAccountService::DEFAULT_CLASSES)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $yearOptions = User::query()
            ->where('role', 'student')
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->filter()
            ->map(fn ($value) => (int) $value)
            ->merge(StudentAccountService::DEFAULT_YEARS)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $studentOptions = User::query()
            ->where('role', 'student')
            ->orderByRaw('COALESCE(fullname, username) asc')
            ->limit(500)
            ->get(['user_id', 'fullname', 'username', 'email']);

        $students = $studentOptions->mapWithKeys(function (User $student) {
            $label = $student->fullname
                ? $student->fullname . ($student->email ? " ({$student->email})" : '')
                : ($student->username ?? $student->email ?? ('ID ' . $student->user_id));

            return [$student->user_id => $label];
        })->all();

        return [
            'classes' => $classOptions,
            'years' => $yearOptions,
            'students' => $students,
        ];
    }

    public function create(array $data, User $admin): Announcement
    {
        $announcement = new Announcement();
        $announcement->title = $data['title'];
        $announcement->slug = $this->generateUniqueSlug($data['title']);
        $announcement->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['content'] ?? ''), 160);
        $announcement->content = $data['content'] ?? null;
        $announcement->type = $data['type'] ?? 'general';
        $announcement->priority = $data['priority'] ?? 'normal';
        $announcement->published_at = Carbon::now();
        $announcement->author_id = $admin->user_id;
        $announcement->target_type = $data['target_type'];
        $announcement->target_filters = $this->buildTargetFilters($data);
        $announcement->sent_at = Carbon::now();
        $announcement->delivered_count = 0;
        $announcement->save();

        $delivered = $this->dispatchNotification($announcement);
        if ($delivered !== null) {
            $announcement->forceFill(['delivered_count' => $delivered])->save();
        }

        return $announcement;
    }

    public function update(Announcement $announcement, array $data): Announcement
    {
        $title = $data['title'];

        if ($title !== $announcement->title) {
            $announcement->slug = $this->generateUniqueSlug($title);
        }

        $announcement->title = $title;
        $announcement->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['content'] ?? ''), 160);
        $announcement->content = $data['content'] ?? null;
        $announcement->type = $data['type'] ?? 'general';
        $announcement->priority = $data['priority'] ?? 'normal';
        $announcement->target_type = $data['target_type'];
        $announcement->target_filters = $this->buildTargetFilters($data);
        $announcement->save();

        return $announcement;
    }

    public function delete(Announcement $announcement): void
    {
        $announcement->delete();
    }

    protected function buildTargetFilters(array $data): ?array
    {
        return match ($data['target_type']) {
            'student' => ['students' => array_map('intval', $data['student_ids'] ?? [])],
            'class' => ['classes' => array_values(array_filter($data['classes'] ?? []))],
            'year' => ['years' => array_map('intval', $data['years'] ?? [])],
            'class_year' => [
                'classes' => array_values(array_filter($data['classes'] ?? [])),
                'years' => array_map('intval', $data['years'] ?? []),
            ],
            default => null,
        };
    }

    protected function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (Announcement::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug ?: Str::random(8);
    }

    protected function dispatchNotification(Announcement $announcement): ?int
    {
        $query = User::query()
            ->where('role', 'student');

        switch ($announcement->target_type) {
            case 'student':
                $ids = $announcement->target_filters['students'] ?? [];
                if (empty($ids)) {
                    return 0;
                }
                $query->whereIn('user_id', $ids);
                break;
            case 'class':
                $classes = $announcement->target_filters['classes'] ?? [];
                if (empty($classes)) {
                    return 0;
                }
                $query->whereIn('class', $classes);
                break;
            case 'year':
                $years = $announcement->target_filters['years'] ?? [];
                if (empty($years)) {
                    return 0;
                }
                $query->whereIn('year', $years);
                break;
            case 'class_year':
                $classes = $announcement->target_filters['classes'] ?? [];
                $years = $announcement->target_filters['years'] ?? [];
                if (empty($classes) || empty($years)) {
                    return 0;
                }
                $query->whereIn('class', $classes)
                    ->whereIn('year', $years);
                break;
            default:
                // all students
                break;
        }

        $total = 0;
        $notification = new StudentAnnouncementPublishedNotification($announcement);
        $smsMessage = $this->buildSmsMessage($announcement);

        $query->select(['user_id', 'fullname', 'username', 'email', 'phone_number'])
            ->chunkById(200, function ($students) use (&$total, $notification, $smsMessage) {
                Notification::send($students, $notification);
                $total += $students->count();

                $numbers = $students->pluck('phone_number')
                    ->filter()
                    ->map(static fn ($value) => (string) $value)
                    ->unique()
                    ->values()
                    ->all();

                if (! empty($numbers) && $smsMessage !== '') {
                    $this->smsService->sendBulk($numbers, $smsMessage);
                }
            }, 'user_id');

        return $total;
    }

    protected function buildSmsMessage(Announcement $announcement): string
    {
        $prefix = '[' . (config('app.name') ?? 'ACSES') . '] ';
        $teaser = $announcement->excerpt
            ?: Str::limit(strip_tags($announcement->content ?? ''), 120);

        if (empty($teaser)) {
            $teaser = __('Log in to review the full update.');
        }

        $body = Str::limit($teaser, 140);

        return $prefix . Str::limit($announcement->title, 60) . ': ' . $body;
    }
}
