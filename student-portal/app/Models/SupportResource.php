<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportResource extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        static::saved(function ($resource) {
            \Illuminate\Support\Facades\Cache::flush();
        });

        static::deleted(function ($resource) {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }

    public const RESOURCE_TYPES = ['link', 'file', 'external'];

    public const CONTENT_TYPES = [
        'handout',
        'past_question',
        'lecture_slide',
        'video',
        'link',
        'guide',
        'policy',
        'other',
    ];

    public const CLASSES = ['Cyber Security', 'Computer Science', 'Information System', 'Robotics'];

    public const YEARS = ['1', '2', '3', '4'];

    protected $fillable = [
        'title',
        'resource_type',
        'content_type',
        'cta_label',
        'cta_url',
        'file_path',
        'description',
        'icon',
        'target_classes',
        'target_years',
    ];

    protected $casts = [
        'target_classes' => 'array',
        'target_years' => 'array',
    ];

    protected $appends = [
        'download_url',
        'is_file',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('title');
    }

    public function scopeForAudience($query, ?string $class, ?string $year)
    {
        return $query
            ->when($class, function ($query) use ($class) {
                $query->where(function ($query) use ($class) {
                    $query->whereNull('target_classes')
                        ->orWhereJsonContains('target_classes', $class);
                });
            })
            ->when($year, function ($query) use ($year) {
                $query->where(function ($query) use ($year) {
                    $query->whereNull('target_years')
                        ->orWhereJsonContains('target_years', $year);
                });
            });
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->file_path);
        }

        return null;
    }

    public function getIsFileAttribute(): bool
    {
        return $this->resource_type === 'file' && ! empty($this->file_path);
    }
}
