<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStudentHasNoOutstandingDues
{
    /**
     * Handle an incoming request.
     *
     * Outstanding dues enforcement is handled at the UI layer via the
     * x-dues-lock-overlay Blade component, which blurs restricted pages
     * and shows a contextual payment prompt. This middleware is retained
     * for future hard-gate use cases (e.g., API routes).
     */
    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request);
    }
}
