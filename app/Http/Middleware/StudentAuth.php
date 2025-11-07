<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('student')->check()) {
            return redirect()->route('student.login')
                ->with('error', 'Please login to access student dashboard.');
        }

        $student = Auth::guard('student')->user();

        if (!$student->is_active) {
            Auth::guard('student')->logout();
            return redirect()->route('student.login')
                ->with('error', 'Your account is inactive. Please contact your school administrator.');
        }

        return $next($request);
    }
}
