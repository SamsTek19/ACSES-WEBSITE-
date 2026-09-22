<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginOtpRequest;
use App\Mail\PendingRegistration\ApplicationReceivedMail;
use App\Models\PendingRegistration;
use App\Services\Registration\PendingRegistrationEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PendingRegistrationVerificationController extends Controller
{
    public function __construct(
        private readonly PendingRegistrationEmailService $emailService
    ) {
    }

    /**
     * Show the OTP verification form for pending registration.
     */
    public function show(Request $request): RedirectResponse|View
    {
        $pending = $request->session()->get('pending_registration_verification');

        if (! $pending || empty($pending['registration_id'])) {
            return redirect()->route('auth.register')
                ->withErrors(['registration' => __('We could not find your registration. Please try again.')]);
        }

        $registration = PendingRegistration::find($pending['registration_id']);

        if (! $registration) {
            $request->session()->forget('pending_registration_verification');
            return redirect()->route('auth.register')
                ->withErrors(['registration' => __('Your registration was not found. Please register again.')]);
        }

        // Already verified - redirect to login
        if ($registration->isEmailVerified()) {
            $request->session()->forget('pending_registration_verification');
            return redirect()->route('login')
                ->with('status', __('Your email has been verified. Please wait for an administrator to review your application.'))
                ->with('pending_registration', true);
        }

        return view('auth.pending-registration-verify', [
            'registration' => $registration,
            'email' => $registration->email,
        ]);
    }

    /**
     * Verify the OTP code for a pending registration.
     */
    public function verify(LoginOtpRequest $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_registration_verification');

        if (! $pending || empty($pending['registration_id'])) {
            return redirect()->route('auth.register')
                ->withErrors(['registration' => __('Your session expired. Please register again.')]);
        }

        $registration = PendingRegistration::find($pending['registration_id']);

        if (! $registration) {
            $request->session()->forget('pending_registration_verification');
            return redirect()->route('auth.register')
                ->withErrors(['registration' => __('Your registration was not found. Please register again.')]);
        }

        $result = $this->emailService->verify($registration, $request->input('code'));

        if (! $result['success']) {
            return back()
                ->withErrors(['code' => $result['message']])
                ->onlyInput('code');
        }

        // Send "Application Received" confirmation email now that email is verified
        try {
            Mail::to($registration->email)->send(new ApplicationReceivedMail($registration));
        } catch (\Exception $e) {
            // Log but don't block the flow
            \Illuminate\Support\Facades\Log::error('Failed to send application received email', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);
        }

        $request->session()->forget('pending_registration_verification');

        return redirect()->route('login')
            ->with('status', __('Your email has been verified! Your registration is now pending administrator approval. You will receive an email once your application has been reviewed.'))
            ->with('pending_registration', true);
    }

    /**
     * Resend the verification code.
     */
    public function resend(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_registration_verification');

        if (! $pending || empty($pending['registration_id'])) {
            return redirect()->route('auth.register')
                ->withErrors(['registration' => __('Your session expired. Please register again.')]);
        }

        $registration = PendingRegistration::find($pending['registration_id']);

        if (! $registration) {
            $request->session()->forget('pending_registration_verification');
            return redirect()->route('auth.register')
                ->withErrors(['registration' => __('Your registration was not found. Please register again.')]);
        }

        if ($registration->isEmailVerified()) {
            $request->session()->forget('pending_registration_verification');
            return redirect()->route('login')
                ->with('status', __('Your email is already verified. Please wait for admin approval.'))
                ->with('pending_registration', true);
        }

        $this->emailService->sendVerification($registration);

        return back()->with('status', __('A fresh verification code has been sent to your email.'));
    }
}

