<?php

namespace App\Services\Student;

use App\Mail\Student\EmailChangeVerificationMail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class StudentEmailChangeService
{
    public function initiate(User $student, string $newEmail): string
    {
        $newEmail = trim($newEmail);

        if ($newEmail === '' || strcasecmp($newEmail, $student->email) === 0) {
            return 'unchanged';
        }

        $taken = User::query()
            ->where('user_id', '!=', $student->getKey())
            ->where(function ($query) use ($newEmail) {
                $query->where('email', $newEmail)
                    ->orWhere('pending_email', $newEmail);
            })
            ->exists();

        if ($taken) {
            return 'taken';
        }

        if ($student->pending_email && strcasecmp($student->pending_email, $newEmail) === 0) {
            $token = $student->email_verification_token;

            if (! $token) {
                $token = Str::uuid()->toString();
                $student->forceFill([
                    'email_verification_token' => $token,
                ])->save();
            }

            $this->dispatchVerificationMail($student, $token);

            return $student->wasChanged('email_verification_token') ? 'initiated' : 'resent';
        }

        $token = Str::uuid()->toString();

        $student->forceFill([
            'pending_email' => $newEmail,
            'email_verification_token' => $token,
        ])->save();

        $this->dispatchVerificationMail($student, $token);

        return 'initiated';
    }

    public function cancel(User $student): void
    {
        if (! $student->pending_email && ! $student->email_verification_token) {
            return;
        }

        $student->forceFill([
            'pending_email' => null,
            'email_verification_token' => null,
        ])->save();
    }

    public function confirm(User $student, string $token): bool
    {
        if (! $student->pending_email || ! $student->email_verification_token) {
            return false;
        }

        if (! hash_equals($student->email_verification_token, $token)) {
            return false;
        }

        $student->forceFill([
            'email' => $student->pending_email,
            'pending_email' => null,
            'email_verification_token' => null,
            'email_verified_at' => Carbon::now(),
        ])->save();

        return true;
    }

    private function dispatchVerificationMail(User $student, string $token): void
    {
        $expiresAt = Carbon::now()->addMinutes(60);
        $verificationUrl = URL::temporarySignedRoute(
            'student.profile.verify-email',
            $expiresAt,
            [
                'user' => $student->getKey(),
                'token' => $token,
            ]
        );

        Mail::to($student->pending_email)->queue(new EmailChangeVerificationMail($student, $verificationUrl));
    }
}
