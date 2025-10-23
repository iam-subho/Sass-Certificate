<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\PasswordChangeRequest;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    /**
     * Show the password change form.
     */
    public function showForm()
    {
        return view('auth.change-password');
    }

    /**
     * Update the user's password.
     */
    public function update(PasswordChangeRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.'
            ])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('password.change')->with('success', 'Password changed successfully.');
    }
}
