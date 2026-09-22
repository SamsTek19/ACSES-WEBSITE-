<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingRegistration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
        'phone_number',
        'index_number',
        'class',
        'year',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'reviewed_at' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the admin who reviewed this registration.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'user_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending registrations (with verified email).
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get pending registrations that are ready for admin review.
     * (email verified and status is pending)
     */
    public function scopeReadyForReview($query)
    {
        return $query->where('status', 'pending')
            ->whereNotNull('email_verified_at');
    }

    /**
     * Scope to get pending registrations awaiting email verification.
     */
    public function scopeAwaitingEmailVerification($query)
    {
        return $query->where('status', 'pending')
            ->whereNull('email_verified_at');
    }

    /**
     * Scope to get approved registrations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected registrations.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if the registration is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the registration was approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the registration was rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the email has been verified.
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if the registration is ready for admin review.
     */
    public function isReadyForReview(): bool
    {
        return $this->isPending() && $this->isEmailVerified();
    }
}

