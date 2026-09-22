<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Password;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The table primary key.
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'fullname',
        'email',
        'password',
        'phone_number',
        'index_number',
        'class',
        'year',
        'is_graduated',
        'department',
        'role',
        'profile_picture',
        'latitude',
        'longitude',
        'is_seller',
        'pending_email',
        'email_verification_token',
        'ec_access',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
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
            'latitude' => 'float',
            'longitude' => 'float',
            'is_seller' => 'boolean',
            'is_graduated' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this->email));
    }

    /**
     * Get the dues for the student.
     */
    public function dues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Due::class, 'student_id', 'user_id');
    }
}
