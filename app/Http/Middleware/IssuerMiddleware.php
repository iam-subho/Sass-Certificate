<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IssuerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow issuers, school admins, and super admins
        if (!$user->isIssuer() && !$user->isSchoolAdmin() && !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
