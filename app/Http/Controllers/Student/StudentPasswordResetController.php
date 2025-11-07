<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class StudentPasswordResetController extends Controller
{
    /**
     * Show the password reset request form.
     */
    public function showResetRequestForm()
    {
        return view('student.auth.forgot-password');
    }

    /**
     * Send password reset link to student's email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:students,email',
        ]);

        $student = Student::where('email', $request->email)->first();

        // Check if account is active
        if (!$student->is_active) {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact your school administrator.',
            ]);
        }

        // Delete existing tokens for this email
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Generate reset token
        $token = Str::random(64);

        // Store token in database
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Send reset link email
        try {
            Mail::send('emails.student-password-reset', [
                'student' => $student,
                'resetUrl' => route('student.password.reset', ['token' => $token, 'email' => $request->email]),
            ], function ($message) use ($student) {
                $message->to($student->email)
                    ->subject('Reset Your Student Portal Password');
            });

            return back()->with('success', 'Password reset link has been sent to your email address.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send reset email. Please try again later.');
        }
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('student.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset the student's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:students,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Verify token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($passwordReset->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        // Update student password
        $student = Student::where('email', $request->email)->first();
        $student->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('student.login')
            ->with('success', 'Password has been reset successfully! You can now login with your new password.');
    }
}
