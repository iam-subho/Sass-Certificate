<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\ImportStudentsRequest;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                $studentData = [
                    'first_name' => $row[0],
                    'last_name' => $row[1],
                    'dob' => $row[2],
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
}
