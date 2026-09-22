<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\PendingRegistration;
use App\Services\Auth\DeviceDetectionService;
use App\Services\Auth\EmailVerificationService;
use App\Services\Auth\LoginOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly LoginOtpService $loginOtpService,
        private readonly EmailVerificationService $emailVerificationService,
        private readonly DeviceDetectionService $deviceDetectionService,
    ) {
    }

    /**
     * Display the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $loginId = trim($request->input('login_id', $request->input('email', '')));
        $password = $request->input('password');
        $remember = $request->boolean('remember');
        $guards = ['admin', 'student'];

        // Check if there's a pending or rejected registration for this login ID (email, username, or index_number)
        $pendingRegistration = PendingRegistration::where(function ($query) use ($loginId) {
                $query->where('email', $loginId)
                    ->orWhere('username', $loginId)
                    ->orWhere('index_number', $loginId);
            })
            ->whereIn('status', ['pending', 'rejected'])
            ->latest()
            ->first();

        if ($pendingRegistration) {
            $message = match ($pendingRegistration->status) {
                'pending' => __('Your registration is still pending approval. Please wait for an administrator to review your application.'),
                'rejected' => __('Your registration request was rejected. Reason: :reason', [
                    'reason' => $pendingRegistration->rejection_reason ?? 'No specific reason provided.',
                ]),
                default => __('Your account access has been restricted. Please contact an administrator.'),
            };

            throw ValidationException::withMessages(['login_id' => $message]);
        }

        foreach ($guards as $guard) {
            $provider = Auth::guard($guard)->getProvider();

            // Find user by email, username, or index_number — scoped to role
            $user = \App\Models\User::where(function ($query) use ($loginId) {
                    $query->where('email', $loginId)
                        ->orWhere('username', $loginId)
                        ->orWhere('index_number', $loginId);
                })
                ->where('role', $guard)
                ->first();

            if (! $user || ! $provider->validateCredentials($user, ['password' => $password])) {
                continue;
            }

            if (method_exists($user, 'getAttribute') && is_null($user->getAttribute('email_verified_at'))) {
                $this->emailVerificationService->send($user);
                $request->session()->put('pending_verification', [
                    'email' => $user->email,
                    'guard' => $guard,
                    'remember' => $remember,
                ]);

                return redirect()->route('auth.verify.notice')
                    ->withErrors(['verification' => __('Please verify your email before signing in. We just sent you a new verification link.')]);
            }

            // Check if device OTP verification is enabled
            $requireDeviceOtp = (bool) config('auth.device_otp', false);

            if (! $requireDeviceOtp || $this->deviceDetectionService->isTrustedDevice($user, $request)) {
                // Update device last used timestamp or register trusted device
                try {
                    if ($this->deviceDetectionService->isTrustedDevice($user, $request)) {
                        $this->deviceDetectionService->touchDevice($user, $request);
                    } else {
                        $this->deviceDetectionService->trustDevice($user, $request);
                    }
                } catch (\Throwable) {
                    // Fail silently so device tracking issues never block login
                }

                // Log the user in directly
                Auth::shouldUse($guard);
                Auth::guard($guard)->login($user, $remember);
                $request->session()->regenerate();

                return redirect()->intended(match ($guard) {
                    'admin' => route('admin.dashboard'),
                    default => route('student.dashboard'),
                })->with('status', __('Welcome back!'));
            }

            // New device detected - require OTP verification
            $this->loginOtpService->send($user, $guard);

            $request->session()->put('pending_login_otp', [
                'user_id' => $user->getAuthIdentifier(),
                'guard' => $guard,
                'remember' => $remember,
                'email' => $user->email,
            ]);

            return redirect()->route('auth.login.otp')
                ->with('status', __('New device detected. We sent a verification code to your email.'));
        }

        throw ValidationException::withMessages([
            'login_id' => __('auth.failed'),
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(): RedirectResponse
    {
        $guard = Auth::getDefaultDriver();
        Auth::guard($guard)->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
        request()->session()->forget(['pending_login_otp']);

        return redirect()->route('login')
            ->with('status', __('You have been logged out.'));
    }
}
