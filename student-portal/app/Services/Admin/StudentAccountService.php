<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentAccountService
{
    public const DEFAULT_CLASSES = [
        'Cyber Security',
        'Information System',
        'Computer Science',
        'Robotics',
    ];

    public const DEFAULT_YEARS = [1, 2, 3, 4];

    public function stats(): array
    {
        $base = User::query()
            ->where('role', 'student')
            ->where('is_graduated', false);

        $totalStudents = (clone $base)->count();

        $rawClassTotals = (clone $base)
            ->select('class', DB::raw('COUNT(*) as total'))
            ->whereNotNull('class')
            ->groupBy('class')
            ->get();

        $classTotals = [];
        foreach ($rawClassTotals as $row) {
            $className = trim((string) $row->class);
            if ($className === '') {
                continue;
            }

            $classTotals[$className] = (int) $row->total;
        }

        $rawClassYearTotals = (clone $base)
            ->select('class', 'year', DB::raw('COUNT(*) as total'))
            ->whereNotNull('class')
            ->whereNotNull('year')
            ->groupBy('class', 'year')
            ->get();

        $classYearTotals = [];
        foreach ($rawClassYearTotals as $row) {
            $className = trim((string) $row->class);
            $year = (int) $row->year;

            if ($className === '' || $year <= 0) {
                continue;
            }

            $classYearTotals[$className][$year] = (int) $row->total;
        }

        $allClasses = collect(self::DEFAULT_CLASSES)
            ->merge(array_keys($classTotals))
            ->merge(array_keys($classYearTotals))
            ->map(static fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $classBreakdown = $allClasses->map(function (string $className) use ($classTotals, $classYearTotals) {
            $yearBuckets = self::DEFAULT_YEARS;
            $years = [];

            foreach ($yearBuckets as $year) {
                $years[$year] = $classYearTotals[$className][$year] ?? 0;
            }

            return [
                'name' => $className,
                'total' => $classTotals[$className] ?? 0,
                'years' => $years,
            ];
        })->values();

        $graduatedBase = User::query()
            ->where('role', 'student')
            ->where('is_graduated', true);

        $totalGraduated = (clone $graduatedBase)->count();

        $rawGraduatedClassTotals = (clone $graduatedBase)
            ->select('class', DB::raw('COUNT(*) as total'))
            ->whereNotNull('class')
            ->groupBy('class')
            ->get();

        $graduatedClassTotals = [];
        foreach ($rawGraduatedClassTotals as $row) {
            $className = trim((string) $row->class);
            if ($className === '') {
                continue;
            }

            $graduatedClassTotals[$className] = (int) $row->total;
        }

        $graduatedClasses = collect(self::DEFAULT_CLASSES)
            ->merge(array_keys($graduatedClassTotals))
            ->map(static fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $graduatedClassBreakdown = $graduatedClasses->map(function (string $className) use ($graduatedClassTotals) {
            return [
                'name' => $className,
                'total' => $graduatedClassTotals[$className] ?? 0,
            ];
        })->values();

        return [
            'total' => $totalStudents,
            'class_breakdown' => $classBreakdown,
            'year_buckets' => self::DEFAULT_YEARS,
            'graduated_total' => $totalGraduated,
            'graduated_class_breakdown' => $graduatedClassBreakdown,
        ];
    }

    public function filterOptions(): array
    {
        $dbClasses = User::query()
            ->where('role', 'student')
            ->where('is_graduated', false)
            ->select('class')
            ->whereNotNull('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        $classOptions = collect(self::DEFAULT_CLASSES)
            ->merge($dbClasses)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $dbYears = User::query()
            ->where('role', 'student')
            ->where('is_graduated', false)
            ->select('year')
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year');

        $yearOptions = collect(self::DEFAULT_YEARS)
            ->merge($dbYears)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->sort()
            ->values();

        return [
            'classes' => $classOptions,
            'years' => $yearOptions,
        ];
    }

    public function studentsQuery(array $filters = []): Builder
    {
        $query = User::query()
            ->where('role', 'student')
            ->where('is_graduated', false);

        if ($search = trim((string) ($filters['search'] ?? ''))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('fullname', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('index_number', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (! empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        return $query->orderByRaw('COALESCE(fullname, username) ASC');
    }

    public function promoteAllStudents(): int
    {
        $updated = 0;
        $maxYear = max(self::DEFAULT_YEARS);

        User::query()
            ->where('role', 'student')
            ->where('is_graduated', false)
            ->whereNotNull('year')
            ->where('year', '>', 0)
            ->chunkById(500, function ($students) use (&$updated, $maxYear) {
                foreach ($students as $student) {
                    $currentYear = (int) $student->year;

                    if ($currentYear <= 0) {
                        continue;
                    }

                    if ($currentYear < $maxYear) {
                        $student->year = $currentYear + 1;
                        $student->save();
                        $updated++;
                    } elseif ($currentYear === $maxYear && ! $student->is_graduated) {
                        $student->is_graduated = true;
                        $student->save();
                    }
                }
            }, 'user_id');

        return $updated;
    }

    public function exportToExcel(Builder $query): StreamedResponse
    {
        $fileName = 'students-' . now()->format('Ymd-His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $columns = [
            'Student ID',
            'Full name',
            'Username',
            'Email',
            'Phone number',
            'Reference number',
            'Class',
            'Year',
            'Created at',
        ];

        $callback = static function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');

            $escape = static function ($value) {
                return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            };

            fwrite($handle, "<table border=\"1\"><thead><tr>");
            foreach ($columns as $column) {
                fwrite($handle, '<th>' . $escape($column) . '</th>');
            }
            fwrite($handle, '</tr></thead><tbody>');

            $query->chunkById(500, function ($students) use ($handle, $escape) {
                foreach ($students as $student) {
                    $row = [
                        $student->getKey(),
                        $student->fullname,
                        $student->username,
                        $student->email,
                        $student->phone_number,
                        $student->index_number,
                        $student->class,
                        $student->year,
                        optional($student->created_at)->format('Y-m-d H:i:s'),
                    ];

                    fwrite($handle, '<tr>');
                    foreach ($row as $value) {
                        fwrite($handle, '<td>' . $escape($value) . '</td>');
                    }
                    fwrite($handle, '</tr>');
                }
            }, 'user_id');

            fwrite($handle, '</tbody></table>');
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
