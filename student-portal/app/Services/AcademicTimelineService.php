<?php

namespace App\Services;

use App\Models\AcademicTimelineEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AcademicTimelineService
{
    public function paginateEntries(int $perPage = 10): LengthAwarePaginator
    {
        return AcademicTimelineEntry::query()
            ->orderByDesc('starts_at')
            ->orderBy('id')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);
    }

    public function listPublished(?string $academicYear = null): Collection
    {
        return AcademicTimelineEntry::query()
            ->when($academicYear, static function ($query) use ($academicYear): void {
                $query->where('academic_year', $academicYear);
            })
            ->published()
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): AcademicTimelineEntry
    {
        return AcademicTimelineEntry::create($data);
    }

    public function update(AcademicTimelineEntry $entry, array $data): AcademicTimelineEntry
    {
        $entry->update($data);

        return $entry;
    }

    public function delete(AcademicTimelineEntry $entry): void
    {
        $entry->delete();
    }
}
