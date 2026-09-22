<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        static::saved(function ($event) {
            \Illuminate\Support\Facades\Cache::flush();
        });

        static::deleted(function ($event) {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }

    protected $fillable = [
        'title',
        'location',
        'description',
        'start_at',
        'end_at',
        'category',
        'cta_url',
        'banner_path',
        'banner_alt',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function getBannerUrlAttribute(): ?string
    {
        if (! $this->banner_path) {
            return null;
        }

        if (Str::startsWith($this->banner_path, ['http://', 'https://', '/'])) {
            return $this->banner_path;
        }

        return asset($this->banner_path);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_at', '>=', Carbon::now())
            ->orderBy('start_at');
    }

}
