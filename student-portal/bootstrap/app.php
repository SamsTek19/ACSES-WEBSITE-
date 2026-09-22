<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Http\Middleware\EnsureStudentHasNoOutstandingDues;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'student.no_outstanding_dues' => EnsureStudentHasNoOutstandingDues::class,
        ]);

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        if (class_exists(\Sentry\Laravel\Integration::class)) {
            \Sentry\Laravel\Integration::handles($exceptions);
        }

        // Handle rate limiting with JSON response for AJAX requests
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                
                return response()->json([
                    'message' => 'Too many attempts. Please try again in ' . $retryAfter . ' seconds.',
                    'retry_after' => (int) $retryAfter,
                    'type' => 'rate_limit',
                ], 429, $e->getHeaders());
            }
        });
    })->create();

