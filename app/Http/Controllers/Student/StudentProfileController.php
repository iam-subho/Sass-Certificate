<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StudentProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $student = Auth::guard('student')->user();
        return view('student.profile.edit', compact('student'));
    }

    /**
     * Update the student's profile information.
     */
    public function update(Request $request)
    {
        $student = Auth::guard('student')->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('students')->ignore($student->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('students')->ignore($student->id),
            ],
            'mobile' => 'required|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'headline' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'profile_public' => 'boolean',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['profile_picture']);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
                Storage::disk('public')->delete($student->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $student->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete profile picture.
     */
    public function deleteProfilePicture()
    {
        $student = Auth::guard('student')->user();

        if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
            Storage::disk('public')->delete($student->profile_picture);
        }

        $student->update(['profile_picture' => null]);

        return back()->with('success', 'Profile picture deleted successfully!');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $student = Auth::guard('student')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $student->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Check if username is available (AJAX endpoint).
     */
    public function checkUsername(Request $request)
    {
        $student = Auth::guard('student')->user();
        $username = $request->input('username');

        $available = Student::isUsernameAvailable($username, $student->id);

        return response()->json(['available' => $available]);
    }
}
