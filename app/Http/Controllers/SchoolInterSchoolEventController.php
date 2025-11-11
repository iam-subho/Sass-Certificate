<?php

namespace App\Http\Controllers;

use App\Models\InterSchoolEvent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolInterSchoolEventController extends Controller
{
    /**
     * Display available inter-school events for the school.
     */
    public function index()
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Get all published events with school's participation status
        $events = InterSchoolEvent::published()
                    ->with(['schools' => function ($query) use ($schoolId) {
                        $query->where('schools.id', $schoolId)
                              ->withPivot(['status', 'can_students_join', 'allowed_classes', 'manual_approval_required', 'joined_at', 'rejected_at']);
                    }])
                    ->withCount(['students' => function ($query) use ($schoolId) {
                        $query->where('students.school_id', $schoolId);
                    }])
                    ->latest('start_date')
                    ->paginate(15);

        return view('school.inter-school-events.index', compact('events'));
    }

    /**
     * Show details of a specific event.
     */
    public function show(InterSchoolEvent $interSchoolEvent)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Load school's participation status
        $event = $interSchoolEvent->load(['schools' => function ($query) use ($schoolId) {
            $query->where('schools.id', $schoolId)
                  ->withPivot(['status', 'can_students_join', 'allowed_classes', 'manual_approval_required', 'joined_at', 'rejected_at']);
        }]);

        $participation = $event->schools->first();

        // Get students from this school who have joined
        $students = $interSchoolEvent->students()
                    ->where('students.school_id', $schoolId)
                    ->with('class:id,name')
                    ->withPivot(['status', 'approved_by_school', 'joined_at'])
                    ->get();

        // Load school's classes for the modal dropdown
        $user->school->load('classes');

        $classes = $user->school->classes;

        return view('school.inter-school-events.show', compact('event', 'participation', 'students', 'classes'));
    }

    /**
     * Join an event.
     */
    public function join(Request $request, InterSchoolEvent $interSchoolEvent)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Validate configuration
        $validated = $request->validate([
            'can_students_join' => 'required|boolean',
            'allowed_classes' => 'nullable|array',
            'allowed_classes.*' => 'exists:classes,id',
            'manual_approval_required' => 'required|boolean',
        ]);

        // Check if already joined or responded
        $existing = DB::table('inter_school_event_school')
                      ->where('inter_school_event_id', $interSchoolEvent->id)
                      ->where('school_id', $schoolId)
                      ->first();

        if ($existing && $existing->status === 'joined') {
            return back()->with('error', 'Your school has already joined this event.');
        }

        // Update or create participation
        $interSchoolEvent->schools()->syncWithoutDetaching([
            $schoolId => [
                'status' => 'joined',
                'can_students_join' => $validated['can_students_join'],
                'allowed_classes' => !empty($validated['allowed_classes']) ? json_encode($validated['allowed_classes']) : null,
                'manual_approval_required' => $validated['manual_approval_required'],
                'responded_by' => $user->id,
                'joined_at' => now(),
                'rejected_at' => null,
            ]
        ]);

        return redirect()->route('school.inter-school-events.show', $interSchoolEvent)
                        ->with('success', 'Successfully joined the event!');
    }

    /**
     * Reject an event.
     */
    public function reject(InterSchoolEvent $interSchoolEvent)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Check if already rejected
        $existing = DB::table('inter_school_event_school')
                      ->where('inter_school_event_id', $interSchoolEvent->id)
                      ->where('school_id', $schoolId)
                      ->first();

        if ($existing && $existing->status === 'rejected') {
            return back()->with('error', 'Your school has already rejected this event.');
        }

        // Update participation status
        $interSchoolEvent->schools()->syncWithoutDetaching([
            $schoolId => [
                'status' => 'rejected',
                'responded_by' => $user->id,
                'rejected_at' => now(),
                'joined_at' => null,
            ]
        ]);

        return redirect()->route('school.inter-school-events.index')
                        ->with('success', 'Event invitation rejected.');
    }

    /**
     * Update event participation settings.
     */
    public function updateSettings(Request $request, InterSchoolEvent $interSchoolEvent)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Check if school has joined
        $participation = DB::table('inter_school_event_school')
                          ->where('inter_school_event_id', $interSchoolEvent->id)
                          ->where('school_id', $schoolId)
                          ->where('status', 'joined')
                          ->first();

        if (!$participation) {
            return back()->with('error', 'Your school has not joined this event.');
        }

        // Validate configuration
        $validated = $request->validate([
            'can_students_join' => 'required|boolean',
            'allowed_classes' => 'nullable|array',
            'allowed_classes.*' => 'exists:classes,id',
            'manual_approval_required' => 'required|boolean',
        ]);

        // Update settings
        $interSchoolEvent->schools()->updateExistingPivot($schoolId, [
            'can_students_join' => $validated['can_students_join'],
            'allowed_classes' => !empty($validated['allowed_classes']) ? json_encode($validated['allowed_classes']) : null,
            'manual_approval_required' => $validated['manual_approval_required'],
        ]);

        return back()->with('success', 'Event participation settings updated successfully.');
    }

    /**
     * Manage students who want to join this event.
     */
    public function manageStudents(InterSchoolEvent $interSchoolEvent)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Check if school has joined
        $participation = DB::table('inter_school_event_school')
                          ->where('inter_school_event_id', $interSchoolEvent->id)
                          ->where('school_id', $schoolId)
                          ->where('status', 'joined')
                          ->first();

        if (!$participation) {
            abort(403, 'Your school has not joined this event.');
        }

        // Get students from this school
        $students = $interSchoolEvent->students()
                    ->where('school_id', $schoolId)
                    ->with('class:id,name')
                    ->withPivot(['status', 'approved_by_school', 'joined_at'])
                    ->paginate(50);

        return view('school.inter-school-events.manage-students', compact('interSchoolEvent', 'students', 'participation'));
    }

    /**
     * Approve a student to participate.
     */
    public function approveStudent(InterSchoolEvent $interSchoolEvent, Student $student)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Verify student belongs to this school
        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized action.');
        }

        // Check if student has joined
        $participation = DB::table('inter_school_event_student')
                          ->where('inter_school_event_id', $interSchoolEvent->id)
                          ->where('student_id', $student->id)
                          ->first();

        if (!$participation) {
            return back()->with('error', 'Student has not joined this event.');
        }

        // Approve student
        $interSchoolEvent->students()->updateExistingPivot($student->id, [
            'approved_by_school' => true,
        ]);

        return back()->with('success', 'Student approved successfully.');
    }

    /**
     * Remove a student from the event.
     */
    public function removeStudent(InterSchoolEvent $interSchoolEvent, Student $student)
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Verify student belongs to this school
        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized action.');
        }

        // Remove student
        $interSchoolEvent->students()->detach($student->id);

        return back()->with('success', 'Student removed from the event.');
    }
}
