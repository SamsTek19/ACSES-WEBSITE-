<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $published_at
 * @property array|null $target_filters
 */
class Announcement extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        static::saved(function ($announcement) {
            \Illuminate\Support\Facades\Cache::flush();
        });

        static::deleted(function ($announcement) {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'type',
        'priority',
        'published_at',
        'author_id',
        'target_type',
        'target_filters',
        'delivered_count',
        'sent_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'sent_at' => 'datetime',
        'target_filters' => 'array',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', Carbon::now());
    }

    public function scopeForStudent(Builder $query, User $student): Builder
    {
        return $query->where(function (Builder $visibility) use ($student) {
            $visibility->where('target_type', 'all')
                ->orWhere(function (Builder $inner) use ($student) {
                    $inner->where('target_type', 'student')
                        ->whereJsonContains('target_filters->students', $student->user_id);
                });

            if ($student->class) {
                $visibility->orWhere(function (Builder $inner) use ($student) {
                    $inner->where('target_type', 'class')
                        ->whereJsonContains('target_filters->classes', $student->class);
                });
            }

            if ($student->year !== null) {
                $visibility->orWhere(function (Builder $inner) use ($student) {
                    $inner->where('target_type', 'year')
                        ->whereJsonContains('target_filters->years', (int) $student->year);
                });
            }

            if ($student->class && $student->year !== null) {
                $visibility->orWhere(function (Builder $inner) use ($student) {
                    $inner->where('target_type', 'class_year')
                        ->whereJsonContains('target_filters->classes', $student->class)
                        ->whereJsonContains('target_filters->years', (int) $student->year);
                });
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isVisibleTo(User $student): bool
    {
        $filters = $this->target_filters ?? [];

        return match ($this->target_type) {
            'all' => true,
            'student' => in_array($student->user_id, $filters['students'] ?? [], true),
            'class' => $student->class !== null && in_array($student->class, $filters['classes'] ?? [], true),
            'year' => $student->year !== null && in_array((int) $student->year, array_map('intval', $filters['years'] ?? []), true),
            'class_year' => $student->class !== null
                && $student->year !== null
                && in_array($student->class, $filters['classes'] ?? [], true)
                && in_array((int) $student->year, array_map('intval', $filters['years'] ?? []), true),
            default => true,
        };
    }
}
