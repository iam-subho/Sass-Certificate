<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    /**
     * Display the public profile of a student.
     */
    public function show($username)
    {
        $student = Student::where('username', $username)
            ->with(['school', 'class'])
            ->firstOrFail();

        // Check if profile is public
        if (!$student->profile_public) {
            abort(403, 'This profile is private.');
        }

        // Get visible certificates
        $certificates = $student->visibleCertificates()
            ->with(['template', 'event', 'school'])
            ->paginate(12);

        return view('student.profile.public', compact('student', 'certificates'));
    }
}
