<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolClass\StoreSchoolClassRequest;
use App\Http\Requests\SchoolClass\UpdateSchoolClassRequest;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $classes = SchoolClass::where('school_id', $user->school_id)
            ->active()
            ->withCount([
                'students',
                /*'students as male_students_count' => function($query) {
                    $query->where('gender', 'male');
                },
                'students as female_students_count' => function($query) {
                    $query->where('gender', 'female');
                }*/
            ])
            ->orderBy('order')
            ->paginate(10);

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(StoreSchoolClassRequest $request)
    {
        $validated = $request->validated();

        SchoolClass::create([
            'school_id' => auth()->user()->school_id,
            'name' => $validated['name'],
            'sections' => $validated['sections'],
            'is_active' => true,
        ]);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    public function edit(SchoolClass $class)
    {
        $user = auth()->user();

        if ($class->school_id != $user->school_id) {
            abort(403);
        }

        return view('classes.edit', compact('class'));
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $class)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->has('is_active');

        $class->update($validated);

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        $user = auth()->user();

        if ($class->school_id != $user->school_id) {
            abort(403);
        }

        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }

    /**
     * Get sections for a class (API endpoint).
     */
    public function getSections($classId)
    {
        $class = SchoolClass::find($classId);

        if (!$class) {
            return response()->json(['sections' => []]);
        }

        // School admin can only access their own school's classes
        $user = auth()->user();
        if ($user->isSchoolAdmin() && $class->school_id != $user->school_id) {
            return response()->json(['sections' => []], 403);
        }

        $sections = $class->sections_array;

        return response()->json(['sections' => $sections]);
    }
}
