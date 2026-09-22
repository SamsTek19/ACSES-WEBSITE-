<?php

namespace App\Providers;

use App\Models\PendingRegistration;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::componentNamespace('App\View\Components', 'app');
        Blade::anonymousComponentNamespace('layouts', 'layouts');

        // Route model binding for PendingRegistration
        Route::model('registration', PendingRegistration::class);

        // Configure rate limiters for authentication
        $this->configureRateLimiters();

        // Access Gate for Maintenance Portal
        \Illuminate\Support\Facades\Gate::define('access-maintenance', function (\App\Models\User $user) {
            if ($user->role !== 'admin') {
                return false;
            }

            // In the future, this can be restricted to specific positions
            // like 'President' or 'Financial Secretary'.
            // For now, we allow all admins to ensure the tools are accessible.
            return true;
        });

        // OWASP: Force HTTPS in production to prevent Cryptographic Failures (data in transit)
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }

    /**
     * Configure rate limiters for authentication endpoints.
     */
    private function configureRateLimiters(): void
    {
        // Login: 5 attempts per minute
        RateLimiter::for('auth-login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return response()->json([
                        'message' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
                        'retry_after' => (int) $seconds,
                        'type' => 'rate_limit',
                    ], 429, $headers);
                });
        });

        // Registration: 3 attempts per minute
        RateLimiter::for('auth-register', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return response()->json([
                        'message' => 'Too many registration attempts. Please try again in ' . $seconds . ' seconds.',
                        'retry_after' => (int) $seconds,
                        'type' => 'rate_limit',
                    ], 429, $headers);
                });
        });

        // Availability check: 60 attempts per minute (more lenient as it checks multiple fields)
        RateLimiter::for('auth-check-availability', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return response()->json([
                        'available' => false,
                        'message' => 'Too many requests. Please wait ' . $seconds . ' seconds.',
                        'retry_after' => (int) $seconds,
                        'type' => 'rate_limit',
                    ], 429, $headers);
                });
        });

        // Password reset: 3 attempts per minute
        RateLimiter::for('auth-password-reset', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return response()->json([
                        'message' => 'Too many password reset attempts. Please try again in ' . $seconds . ' seconds.',
                        'retry_after' => (int) $seconds,
                        'type' => 'rate_limit',
                    ], 429, $headers);
                });
        });

        // OTP verification: 5 attempts per minute
        RateLimiter::for('auth-otp', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return response()->json([
                        'message' => 'Too many verification attempts. Please try again in ' . $seconds . ' seconds.',
                        'retry_after' => (int) $seconds,
                        'type' => 'rate_limit',
                    ], 429, $headers);
                });
        });

        // Resend OTP/email: 2 attempts per minute  
        RateLimiter::for('auth-resend', function (Request $request) {
            return Limit::perMinute(2)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return response()->json([
                        'message' => 'Please wait before requesting another code. Try again in ' . $seconds . ' seconds.',
                        'retry_after' => (int) $seconds,
                        'type' => 'rate_limit',
                    ], 429, $headers);
                });
        });
    }
}
