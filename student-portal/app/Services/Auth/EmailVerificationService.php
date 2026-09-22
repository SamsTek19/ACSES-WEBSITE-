<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Carbon;

class EmailVerificationService
{
    public function __construct(private readonly LoginOtpService $otpService)
    {
    }

    /**
     * Send or resend an email verification code to the given user.
     */
    public function send(User $user, bool $resetState = true): void
    {
        if ($resetState) {
            $user->forceFill([
                'email_verification_token' => null,
                'email_verified_at' => null,
            ])->save();

            $this->otpService->clear($user, 'verification');
        }

        $this->otpService->send($user, 'verification', 'verification');
    }

    /**
     * Validate an email verification code and mark the user as verified if it is correct.
     *
     * @return array{success:bool,message:string|null}
     */
    public function verify(User $user, string $code): array
    {
        $result = $this->otpService->verify($user, 'verification', $code);

        if (! $result['success']) {
            return $result;
        }

        $user->forceFill([
            'email_verified_at' => Carbon::now(),
            'email_verification_token' => null,
        ])->save();

        return ['success' => true, 'message' => null];
    }
}
