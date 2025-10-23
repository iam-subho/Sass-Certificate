<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSchoolActive
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip check for super admin (they don't have a school)
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has a school and if it's active
        if ($user && $user->school_id) {
            $school = $user->school;

            // If school is not active, logout the user
            if ($school && !$school->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();


                return redirect()->route('login')->withErrors([
                    'email' => 'Your account is inactive. Please contact administrator.',
                ]);
            }
        }

        return $next($request);
    }
}
