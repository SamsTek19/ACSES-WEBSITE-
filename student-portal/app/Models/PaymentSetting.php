<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $table = 'payment_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
        'updated_by',
    ];

    public $timestamps = false; // The table has a custom timestamp logic in migration

    public static function get(string $key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function set(string $key, $value, ?string $description = null, ?int $updatedBy = null)
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'description' => $description,
                'updated_by' => $updatedBy,
            ]
        );
    }
}
