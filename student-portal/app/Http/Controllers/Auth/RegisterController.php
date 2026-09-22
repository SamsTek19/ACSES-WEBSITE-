<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Services\Admin\AdminDueService;
use App\Services\Auth\EmailVerificationService;
use App\Services\Registration\PendingRegistrationEmailService;
use App\Services\Registration\PendingRegistrationService;
use App\Services\Registration\StudentEmailValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        private readonly AdminDueService $dues,
        private readonly StudentEmailValidator $emailValidator,
        private readonly PendingRegistrationService $pendingService,
        private readonly PendingRegistrationEmailService $pendingEmailService,
    ) {
    }

    /**
     * Show the student registration form.
     */
    public function create(): View
    {
        return view('auth.register', [
            'schoolDomain' => StudentEmailValidator::getSchoolDomain(),
            'classPrefixes' => StudentEmailValidator::getValidPrefixes(),
        ]);
    }

    /**
     * Check if a field value is available.
     * 
     * This performs real-time validation to prevent duplicate errors.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $field = $request->input('field');
        $value = trim($request->input('value', ''));

        if (!in_array($field, ['username', 'email', 'phone_number', 'index_number'])) {
            return response()->json(['available' => false, 'message' => 'Invalid field.'], 400);
        }

        // 1. Format Validation
        switch ($field) {
            case 'username':
                if (strlen($value) < 3) return $this->unavailable('Username must be at least 3 characters.');
                if (strlen($value) > 50) return $this->unavailable('Username must be 50 characters or less.');
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $value)) return $this->unavailable('Username can only contain letters, numbers, dashes, and underscores.');
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) return $this->unavailable('Please enter a valid email address.');
                
                $allowedDomains = ['st.umat.edu.gh', 'umat.edu.gh', 'gmail.com', 'icloud.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'live.com', 'msn.com'];
                $domain = substr(strrchr($value, "@"), 1);
                if (!in_array($domain, $allowedDomains)) {
                    return $this->unavailable('Registration is restricted to school, Gmail, or iCloud accounts.');
                }
                break;

            case 'phone_number':
                if (!preg_match('/^\d{9,11}$/', $value)) return $this->unavailable('Phone number must be between 9 and 11 digits.');
                break;

            case 'index_number':
                if (strlen($value) < 9) {
                    return $this->unavailable('Student ID / Reference number is incomplete.');
                }
                if (!preg_match('/^[a-zA-Z0-9\.\-\/]{9,30}$/', $value)) {
                    return $this->unavailable('Student ID / Reference number contains invalid characters.');
                }
                break;
        }

        // 2. Database Availability Check
        $fieldLabel = ($field === 'index_number') ? 'Index / Reference number' : str_replace('_', ' ', $field);

        // Check in users table
        $existsInUsers = User::where($field, $value)->exists();
        if ($existsInUsers) {
            return $this->unavailable("This {$fieldLabel} is already registered. Please login or contact the administrator.");
        }

        // Check in pending_registrations table (only pending ones with verified email)
        $existsInPending = PendingRegistration::where($field, $value)
            ->pending()
            ->whereNotNull('email_verified_at')
            ->exists();

        if ($existsInPending) {
            return $this->unavailable("This {$fieldLabel} is pending approval. Please contact the administrator.");
        }

        return response()->json([
            'available' => true,
            'message' => ucfirst($fieldLabel) . ' is available!',
        ]);
    }

    private function unavailable(string $message): JsonResponse
    {
        return response()->json([
            'available' => false,
            'message' => $message,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * This method implements dual-flow registration:
     * 1. School email (st.umat.edu.gh with matching prefix): Standard verification flow
     * 2. Non-school email or mismatched prefix: Email verification first, then manual admin review
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // 1. Rate Limiting (Prevent Bot Spam)
        $rateKey = 'register-attempt:' . $request->ip();
        if (cache()->has($rateKey) && cache()->get($rateKey) >= 3) {
            return redirect()->back()
                ->with('error', __('Too many registration attempts. Please wait 15 minutes.'))
                ->withInput();
        }
        cache()->put($rateKey, (cache()->get($rateKey, 0) + 1), now()->addMinutes(15));

        $data = $request->validated();
        $email = strtolower($data['email']);
        $fullName = trim(implode(' ', array_filter([
            $data['first_name'] ?? '',
            $data['other_name'] ?? '',
            $data['last_name'] ?? '',
        ])));
        $class = $data['class'];

        // 2. Domain Restriction (Anti-Bot / Anti-Cheating)
        $allowedDomains = ['st.umat.edu.gh', 'umat.edu.gh', 'gmail.com', 'icloud.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'live.com', 'msn.com'];
        $domain = substr(strrchr($email, "@"), 1);
        
        if (!in_array($domain, $allowedDomains)) {
            return redirect()->back()
                ->withErrors(['email' => __('Registration is only allowed using school emails or trusted providers (Gmail, Outlook, iCloud, Yahoo, etc).')])
                ->withInput();
        }

        // Check for email/class mismatch if using school email
        if ($this->emailValidator->isSchoolEmail($email)) {
            $mismatchMessage = $this->emailValidator->getMismatchMessage($email, $class);
            if ($mismatchMessage) {
                return redirect()->route('auth.register')
                    ->withErrors(['email' => $mismatchMessage])
                    ->withInput($request->except('password', 'password_confirmation'));
            }
        }

        // 3. Duplicate Checks
        // Note: The RegisterRequest handles the primary unique:users checks. 
        // This is a secondary safeguard.
        $existingUser = User::where('email', $email)->orWhere('index_number', $data['index_number'])->first();

        if ($existingUser) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => __('An account already exists for this email or reference number. Please log in or use the forgot password feature.'),
                ]);
        }

        // Check for existing pending registrations (with verified email only)
        $existingPending = PendingRegistration::where(function ($query) use ($email, $data) {
                $query->where('email', $email)
                    ->orWhere('index_number', $data['index_number']);
            })
            ->pending()
            ->whereNotNull('email_verified_at')
            ->first();

        if ($existingPending) {
            return redirect()->route('auth.register')
                ->withErrors([
                    'email' => __('A registration request with this email or reference number is likely already pending. Please contact the administrator for assistance.'),
                ])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Determine registration flow based on email
        if ($this->emailValidator->canAutoVerify($email, $class)) {
            // Standard flow: School email with matching prefix
            return $this->handleStandardRegistration($request, $data, $fullName);
        } else {
            // Manual verification flow: Non-school email - verify email first, then admin review
            return $this->handlePendingRegistration($request, $data, $fullName);
        }
    }

    /**
     * Handle standard registration with email verification.
     */
    private function handleStandardRegistration(
        RegisterRequest $request,
        array $data,
        string $fullName
    ): RedirectResponse {
        // Since RegisterRequest enforces unique constraints, we can safely create the user.
        $user = User::create([
            'fullname' => $fullName,
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'] ?? null,
            'index_number' => $data['index_number'],
            'class' => $data['class'],
            'year' => $data['year'],
            'role' => 'student',
        ]);

        $this->dues->syncStudent($user);

        app(EmailVerificationService::class)->send($user);

        $request->session()->put('pending_verification', [
            'email' => $user->email,
            'guard' => 'student',
            'user_id' => $user->getAuthIdentifier(),
            'remember' => false,
        ]);

        return redirect()->route('auth.verify.notice')
            ->with('status', __('Registration successful. Please verify your email to activate your account.'));
    }

    /**
     * Handle pending registration that requires admin approval.
     * First sends email verification, only after which the registration goes to admin queue.
     */
    private function handlePendingRegistration(Request $request, array $data, string $fullName): RedirectResponse
    {
        // Check for existing unverified pending registration with same email
        $existingUnverified = PendingRegistration::where('email', $data['email'])
            ->whereNull('email_verified_at')
            ->pending()
            ->first();

        if ($existingUnverified) {
            // Resend verification to existing unverified registration
            $this->pendingEmailService->sendVerification($existingUnverified);

            $request->session()->put('pending_registration_verification', [
                'registration_id' => $existingUnverified->id,
                'email' => $existingUnverified->email,
            ]);

            return redirect()->route('auth.pending-registration.verify')
                ->with('status', __('A verification code has been resent to your email.'));
        }

        // Create new pending registration (without email_verified_at = needs verification)
        $registration = PendingRegistration::create([
            'fullname' => $fullName,
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'] ?? null,
            'index_number' => $data['index_number'],
            'class' => $data['class'],
            'year' => $data['year'],
            'status' => 'pending',
            // email_verified_at is NULL - will be set after OTP verification
        ]);

        // Send email verification OTP
        $this->pendingEmailService->sendVerification($registration);

        // Store in session for verification
        $request->session()->put('pending_registration_verification', [
            'registration_id' => $registration->id,
            'email' => $registration->email,
        ]);

        return redirect()->route('auth.pending-registration.verify')
            ->with('status', __('Please verify your email address to complete your registration.'));
    }
}

