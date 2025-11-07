<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StudentAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('student.auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Can be email or username
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (Auth::guard('student')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $student = Auth::guard('student')->user();

            if (!$student->is_active) {
                Auth::guard('student')->logout();
                return back()->withErrors([
                    'login' => 'Your account is inactive. Please contact your school administrator.',
                ])->withInput();
            }

            // Update last login timestamp
            $student->update(['last_login_at' => now()]);

            return redirect()->route('student.dashboard')
                ->with('success', 'Welcome back, ' . $student->first_name . '!');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('student.auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'mobile' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'dob' => 'required|date|before:today',
        ]);

        // Generate unique username
        $username = Student::generateUsername($request->first_name, $request->last_name);

        $student = Student::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'username' => $username,
            'dob' => $request->dob,
            'is_active' => false, // Needs school admin approval
            'father_name' => $request->father_name ?? '',
            'mother_name' => $request->mother_name ?? '',
        ]);

        return redirect()->route('student.login')
            ->with('success', 'Registration successful! Your account is pending approval from your school. You will be able to login once approved.');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')
            ->with('success', 'You have been logged out successfully.');
    }
}
