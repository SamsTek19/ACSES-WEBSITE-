<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginOtpRequest;
use App\Models\User;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function __construct(private readonly EmailVerificationService $verificationService)
    {
    }

    /**
     * Show the verification notice page.
     */
    public function notice(Request $request): View
    {
        $pending = $request->session()->get('pending_verification');

        return view('auth.verify-notice', [
            'pending' => $pending,
        ]);
    }

    /**
     * Handle verification of an email OTP code.
     */
    public function verify(LoginOtpRequest $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_verification');

        if (! $pending || empty($pending['email']) || empty($pending['guard'])) {
            return redirect()->route('auth.register')
                ->withErrors(['verification' => __('We could not determine which account to verify. Please register again.')]);
        }

        $user = User::where('email', $pending['email'])->first();

        if (! $user) {
            $request->session()->forget('pending_verification');

            return redirect()->route('auth.register')
                ->withErrors(['verification' => __('We could not find an account for that email. Please register again.')]);
        }

        $result = $this->verificationService->verify($user, $request->input('code'));

        if (! $result['success']) {
            return back()->withErrors(['code' => $result['message']])->onlyInput('code');
        }

        $guard = $pending['guard'];
        $remember = (bool) ($pending['remember'] ?? false);

        $request->session()->forget('pending_verification');

        Auth::guard($guard)->login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(match ($guard) {
            'admin' => route('admin.dashboard'),
            default => route('student.dashboard'),
        })->with('status', __('Email verified! Welcome to the ACSES Portal.'));
    }

    /**
     * Resend a verification email to the pending user.
     */
    public function resend(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_verification');

        if (! $pending || empty($pending['email']) || empty($pending['guard'])) {
            return redirect()->route('auth.register')
                ->withErrors(['verification' => __('We could not determine which account to verify. Please register again.')]);
        }

        $user = User::where('email', $pending['email'])->first();

        if (! $user) {
            $request->session()->forget('pending_verification');

            return redirect()->route('auth.register')
                ->withErrors(['verification' => __('We could not find an account for that email. Please register again.')]);
        }

        $this->verificationService->send($user);

        return back()->with('status', __('A fresh verification code has been sent to your email.'));
    }
}
