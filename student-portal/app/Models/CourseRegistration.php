<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'status',
        'progress_percent',
        'pending_documents',
        'submitted_at',
        'approved_at',
        'document_paths',
        'admin_comment',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'pending_documents' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'document_paths' => 'array',
        'admin_comment' => 'string',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}
