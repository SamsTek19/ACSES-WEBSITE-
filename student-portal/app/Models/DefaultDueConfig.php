<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultDueConfig extends Model
{
    use HasFactory;

    protected $table = 'default_dues_config';

    protected $primaryKey = 'config_id';

    protected $fillable = [
        'class',
        'year',
        'target_group',
        'amount',
        'description',
        'due_date_offset',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date_offset' => 'integer',
        'is_active' => 'boolean',
    ];
}
