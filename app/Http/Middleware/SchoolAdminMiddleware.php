<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isSchoolAdmin()) {
            abort(403, 'Unauthorized. School Admin access required.');
        }

        if (!auth()->user()->is_active) {
            abort(403, 'Your account is inactive. Please contact administrator.');
        }

        return $next($request);
    }
}
