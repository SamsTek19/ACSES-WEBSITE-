<?php

namespace App\Services\Registration;

use App\Mail\PendingRegistration\EmailVerificationMail;
use App\Models\LoginOtpCode;
use App\Models\PendingRegistration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PendingRegistrationEmailService
{
    private const EXPIRATION_MINUTES = 15;
    private const MAX_ATTEMPTS = 5;

    /**
     * Send email verification OTP to a pending registration.
     */
    public function sendVerification(PendingRegistration $registration): void
    {
        $code = $this->generateCode($registration);

        Mail::to($registration->email)->send(new EmailVerificationMail(
            registration: $registration,
            code: $code,
            expiresInMinutes: self::EXPIRATION_MINUTES
        ));
    }

    /**
     * Verify the OTP code for a pending registration.
     *
     * @return array{success: bool, message: string|null}
     */
    public function verify(PendingRegistration $registration, string $code): array
    {
        $guard = 'pending_registration_' . $registration->id;

        $record = LoginOtpCode::where('user_id', $registration->id)
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

        // Success - mark email as verified
        $record->delete();

        $registration->update([
            'email_verified_at' => Carbon::now(),
        ]);

        return ['success' => true, 'message' => null];
    }

    /**
     * Generate and store an OTP code.
     */
    private function generateCode(PendingRegistration $registration): string
    {
        $guard = 'pending_registration_' . $registration->id;

        // Clear any existing codes
        LoginOtpCode::where('user_id', $registration->id)
            ->where('guard', $guard)
            ->delete();

        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::EXPIRATION_MINUTES);

        LoginOtpCode::create([
            'user_id' => $registration->id,
            'guard' => $guard,
            'code_hash' => Hash::make($code),
            'expires_at' => $expiresAt,
        ]);

        return $code;
    }

    /**
     * Clear pending verification codes for a registration.
     */
    public function clearCodes(PendingRegistration $registration): void
    {
        $guard = 'pending_registration_' . $registration->id;

        LoginOtpCode::where('user_id', $registration->id)
            ->where('guard', $guard)
            ->delete();
    }
}
