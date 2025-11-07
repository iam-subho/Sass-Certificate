<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\ImportStudentsRequest;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $students = Student::with(['school:id,name', 'class:id,name'])->paginate(20);
        } else {
            $students = Student::with(['class:id,name'])
                ->where('school_id', $user->school_id)
                ->paginate(20);
        }

        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $schools = School::where('is_active', true)->select('id', 'name')->get();
            $classes = \App\Models\SchoolClass::select('id', 'name', 'school_id')->get();
        } else {
            $schools = School::where('id', $user->school_id)->select('id', 'name')->get();
            $classes = \App\Models\SchoolClass::where('school_id', $user->school_id)
                ->select('id', 'name', 'school_id')->get();
        }

        return view('students.create', compact('schools', 'classes'));
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        // School admin can only create students for their school
        if ($user->isSchoolAdmin() && $validated['school_id'] != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $user = auth()->user();

        // School admin can only edit their own school's students
        if ($user->isSchoolAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->isSuperAdmin()) {
            $schools = School::where('is_active', true)->select('id', 'name')->get();
            $classes = \App\Models\SchoolClass::select('id', 'name', 'school_id')->get();
        } else {
            $schools = School::where('id', $user->school_id)->select('id', 'name')->get();
            $classes = \App\Models\SchoolClass::where('school_id', $user->school_id)
                ->select('id', 'name', 'school_id')->get();
        }

        return view('students.edit', compact('student', 'schools', 'classes'));
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $user = auth()->user();

        // School admin can only update their own school's students
        if ($user->isSchoolAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        // School admin cannot change school_id
        if ($user->isSchoolAdmin() && $validated['school_id'] != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        $user = auth()->user();

        // School admin can only delete their own school's students
        if ($user->isSchoolAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Show import CSV form.
     */
    public function importForm()
    {
        return view('students.import');
    }

    /**
     * Import students from CSV.
     */
    public function import(ImportStudentsRequest $request)
    {
        $user = auth()->user();

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $header = array_shift($data); // Remove header row

        $imported = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            if (count($row) < 8) {
                $errors[] = "Row " . ($index + 2) . ": Incomplete data";
                continue;
            }

            try {
                $firstName = $row[0];
                $lastName = $row[1];
                $dob = $row[2];

                $studentData = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'dob' => $dob,
                    'father_name' => $row[3],
                    'mother_name' => $row[4],
                    'mobile' => $row[5],
                    'email' => $row[6] ?? null,
                    'school_id' => $user->isSchoolAdmin() ? $user->school_id : $row[7],
                    'class_id' => $row[8] ?? null,
                    'section' => $row[9] ?? null,
                ];

                // Validate school_id for school admin
                if ($user->isSchoolAdmin() && !empty($row[7]) && $row[7] != $user->school_id) {
                    $errors[] = "Row " . ($index + 2) . ": Cannot import students for other schools";
                    continue;
                }

                // Auto-generate credentials for imported students
                $studentData['username'] = Student::generateUsername($firstName, $lastName);
                $studentData['password'] = Hash::make(\Carbon\Carbon::parse($dob)->format('dmY')); // DOB as default password
                $studentData['is_active'] = true; // Auto-activate imported students

                Student::create($studentData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        $message = "$imported students imported successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()->route('students.index')
            ->with('success', $message);
    }

    /**
     * Generate login credentials for a student.
     */
    public function generateCredentials(Student $student)
    {
        $user = auth()->user();

        // School admin can only generate credentials for their own school's students
        if ($user->isSchoolAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        // Generate username if not exists
        if (!$student->username) {
            $student->username = Student::generateUsername($student->first_name, $student->last_name);
        }

        // Generate temporary password (using DOB format: DDMMYYYY)
        $tempPassword = $student->dob->format('dmY').rand(111111,999999);

        // Update student with credentials and activate account
        $student->update([
            'username' => $student->username,
            'password' => Hash::make($tempPassword),
            'is_active' => true,
        ]);

        // Return the temporary password so admin can share it
        return back()->with('success', "Credentials generated successfully! Username: {$student->username}, Temporary Password: {$tempPassword}");
    }

    /**
     * Send login credentials to student via email.
     */
    public function sendCredentials(Student $student)
    {
        $user = auth()->user();

        // School admin can only send credentials for their own school's students
        if ($user->isSchoolAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if student has credentials
        if (!$student->username || !$student->password) {
            return back()->with('error', 'Please generate credentials first.');
        }

        // Check if student has email
        if (!$student->email) {
            return back()->with('error', 'Student does not have an email address.');
        }

        try {
            $tempPassword = $student->dob->format('dmY').rand(111111,999999);
            $student->update([
                'password' => Hash::make($tempPassword),
            ]);

            // Send email with login credentials
            Mail::send('emails.student-credentials', [
                'password' => $tempPassword,
                'student' => $student,
                'loginUrl' => route('student.login'),
            ], function ($message) use ($student) {
                $message->to($student->email)
                    ->subject('Your Student Portal Login Credentials');
            });

            return back()->with('success', 'Login credentials sent to student email successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Toggle student account active status.
     */
    public function toggleActive(Student $student)
    {
        $user = auth()->user();

        // School admin can only toggle status for their own school's students
        if ($user->isSchoolAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $student->update([
            'is_active' => !$student->is_active,
        ]);

        $status = $student->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Student account {$status} successfully.");
    }

    /**
     * Reset student password to default (DOB).
     */
    public function resetPassword(Student $student)
    {
        $user = auth()->user();

        // School admin can only reset password for their own school's students
        if ($user->isSchoolAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        // Reset password to DOB format
        $tempPassword = $student->dob->format('dmY').rand(111111,999999);

        $student->update([
            'password' => Hash::make($tempPassword),
        ]);

        return back()->with('success', "Password reset successfully! New password: {$tempPassword}");
    }
}
