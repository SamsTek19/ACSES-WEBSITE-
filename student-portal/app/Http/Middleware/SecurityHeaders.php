<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Apply headers only if the response supports it
        if (method_exists($response, 'headers')) {
            // OWASP: Prevent Clickjacking
            $response->headers->set('X-Frame-Options', 'DENY');
            
            // OWASP: Prevent MIME-sniffing
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            
            // OWASP: Strict Transport Security (HSTS)
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            
            // OWASP: Referrer Policy
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        return $response;
    }
}
