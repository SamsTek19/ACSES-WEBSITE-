<?php

namespace App\Services\Auth;

use App\Mail\LoginOtpCodeMail;
use App\Models\LoginOtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginOtpService
{
    private const EXPIRATION_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;

    /**
     * Generate and email an OTP code for the given user and guard.
     */
    public function send(User $user, string $guard, string $context = 'login'): void
    {
        $code = $this->generateCode($user, $guard);

        // Send immediately instead of queuing for critical auth emails
        Mail::to($user->email)->send(new LoginOtpCodeMail($user, $code, self::EXPIRATION_MINUTES, $context));
    }

    /**
     * Verify an OTP code against the latest record for the given user and guard.
     *
     * @return array{success:bool,message:string|null}
     */
    public function verify(User $user, string $guard, string $code): array
    {
        $record = LoginOtpCode::where('user_id', $user->getAuthIdentifier())
            ->where('guard', $guard)
            ->latest()
            ->first();

        if (! $record) {
            return ['success' => false, 'message' => __('No active verification code was found. Please request a new one.')];
        }

        if ($record->expires_at->isPast()) {
            $record->delete();

            return ['success' => false, 'message' => __('That code has expired. Please request a new one.')];
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            $record->delete();

            return ['success' => false, 'message' => __('Too many attempts. Please request a fresh verification code.')];
        }

        if (! Hash::check($code, $record->code_hash)) {
            $record->increment('attempts');
            $remaining = max(self::MAX_ATTEMPTS - $record->attempts, 0);

            return ['success' => false, 'message' => __('Invalid code. :count attempts remaining.', ['count' => $remaining])];
        }

        $record->delete();

        return ['success' => true, 'message' => null];
    }

    /**
     * Remove any outstanding OTP codes for the given user and guard.
     */
    public function clear(User $user, string $guard): void
    {
        LoginOtpCode::where('user_id', $user->getAuthIdentifier())
            ->where('guard', $guard)
            ->delete();
    }

    private function generateCode(User $user, string $guard): string
    {
        $this->clear($user, $guard);

        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::EXPIRATION_MINUTES);

        LoginOtpCode::create([
            'user_id' => $user->getAuthIdentifier(),
            'guard' => $guard,
            'code_hash' => Hash::make($code),
            'expires_at' => $expiresAt,
        ]);

        return $code;
    }
}
