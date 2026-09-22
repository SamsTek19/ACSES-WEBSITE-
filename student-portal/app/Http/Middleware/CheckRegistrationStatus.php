<?php

namespace App\Http\Middleware;

use App\Models\PendingRegistration;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationStatus
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the user attempting to log in has a
     * pending or rejected registration status. If so, login is blocked.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This middleware should be applied after authentication
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Check for pending or rejected registration by email
        $pendingRegistration = PendingRegistration::where('email', $user->email)
            ->whereIn('status', ['pending', 'rejected'])
            ->latest()
            ->first();

        if ($pendingRegistration) {
            // Force logout
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = match ($pendingRegistration->status) {
                'pending' => __('Your registration is still pending approval. Please wait for an administrator to review your application.'),
                'rejected' => __('Your registration has been rejected. Reason: :reason', [
                    'reason' => $pendingRegistration->rejection_reason ?? 'No reason provided.',
                ]),
                default => __('Your account access has been restricted. Please contact an administrator.'),
            };

            return redirect()->route('login')
                ->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
