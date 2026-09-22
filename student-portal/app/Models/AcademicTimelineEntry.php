<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AcademicTimelineEntry extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        static::saved(function ($entry) {
            \Illuminate\Support\Facades\Cache::flush();
        });

        static::deleted(function ($entry) {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }

    protected $fillable = [
        'title',
        'starts_at',
        'academic_year',
        'is_published',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcomingFirst(Builder $query): Builder
    {
        return $query->orderBy('starts_at');
    }

    public function isPast(): bool
    {
        return optional($this->starts_at)->lt(Carbon::now());
    }
}
