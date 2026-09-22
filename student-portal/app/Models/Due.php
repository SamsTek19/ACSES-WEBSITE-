<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Due extends Model
{
    use HasFactory;

    protected $table = 'dues';

    public static function boot()
    {
        parent::boot();

        static::saved(function ($due) {
            \Illuminate\Support\Facades\Cache::forget("student_actions_meta_{$due->student_id}");
            \Illuminate\Support\Facades\Cache::forget("student_dues_summary_{$due->student_id}");
        });

        static::deleted(function ($due) {
            \Illuminate\Support\Facades\Cache::forget("student_actions_meta_{$due->student_id}");
            \Illuminate\Support\Facades\Cache::forget("student_dues_summary_{$due->student_id}");
        });
    }

    protected $primaryKey = 'due_id';

    protected $fillable = [
        'student_id',
        'description',
        'amount',
        'due_date',
        'academic_year',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_date',
        'verification_date',
        'verified_by',
        'verification_notes',
        'is_active',
        'network',
        'reference_number',
        'payment_notes',
        'recorded_by',
        'rejection_reason',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'datetime',
        'verification_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'due_id';
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    public function scopeOutstanding($query)
    {
        return $query->whereIn('payment_status', ['owing', 'pending_verification'])
            ->where('is_active', true);
    }

    public function dueDate(): ?CarbonInterface
    {
        return $this->getAttribute('due_date');
    }
}
