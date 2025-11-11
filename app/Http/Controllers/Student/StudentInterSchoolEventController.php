<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\InterSchoolEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentInterSchoolEventController extends Controller
{
    /**
     * Display inter-school events available to the student.
     * Only shows events that the student's school has joined.
     */
    public function index()
    {
        $student = auth()->guard('student')->user();
        $schoolId = $student->school_id;

        // Get events that the student's school has joined
        $events = InterSchoolEvent::published()
                    ->whereHas('schools', function ($query) use ($schoolId) {
                        $query->where('school_id', $schoolId)
                              ->where('status', 'joined');
                    })
                    ->with(['schools' => function ($query) use ($schoolId) {
                        $query->where('school_id', $schoolId)
                              ->withPivot(['can_students_join', 'allowed_classes', 'manual_approval_required']);
                    }])
                    ->withCount(['students' => function ($query) use ($schoolId) {
                        $query->where('school_id', $schoolId);
                    }])
                    ->latest('start_date')
                    ->get();

        // Filter events based on class restrictions and check if student has joined
        $availableEvents = $events->map(function ($event) use ($student) {
            $participation = $event->schools->first();

            // Check if student can join based on class restrictions
            $canJoin = true;
            if ($participation && $participation->pivot->allowed_classes) {
                $allowedClasses = json_decode($participation->pivot->allowed_classes, true);
                $canJoin = in_array($student->class_id, $allowedClasses);
            }

            // Check if student has already joined
            $hasJoined = $event->hasStudentJoined($student->id);

            $event->canJoin = $canJoin && $participation && $participation->pivot->can_students_join;
            $event->hasJoined = $hasJoined;
            $event->schoolParticipation = $participation;

            return $event;
        });

        return view('student.inter-school-events.index', compact('availableEvents'));
    }

    /**
     * Show details of a specific event.
     */
    public function show(InterSchoolEvent $interSchoolEvent)
    {
        $student = auth()->guard('student')->user();
        $schoolId = $student->school_id;

        // Check if student's school has joined
        $schoolParticipation = DB::table('inter_school_event_school')
                                ->where('inter_school_event_id', $interSchoolEvent->id)
                                ->where('school_id', $schoolId)
                                ->where('status', 'joined')
                                ->first();

        if (!$schoolParticipation) {
            abort(403, 'Your school has not joined this event.');
        }

        // Load event details
        $event = $interSchoolEvent->load(['creator:id,name']);

        // Check if student can join based on class restrictions
        $canJoin = true;
        if ($schoolParticipation->allowed_classes) {
            $allowedClasses = json_decode($schoolParticipation->allowed_classes, true);
            $canJoin = in_array($student->class_id, $allowedClasses);
        }

        // Check if student has already joined
        $studentParticipation = DB::table('inter_school_event_student')
                                  ->where('inter_school_event_id', $interSchoolEvent->id)
                                  ->where('student_id', $student->id)
                                  ->first();

        $hasJoined = (bool) $studentParticipation;
        $approvedBySchool = $studentParticipation ? $studentParticipation->approved_by_school : false;

        // Get total students from this school
        $schoolStudentCount = $interSchoolEvent->students()
                                ->where('school_id', $schoolId)
                                ->count();

        return view('student.inter-school-events.show', compact(
            'event',
            'schoolParticipation',
            'canJoin',
            'hasJoined',
            'approvedBySchool',
            'schoolStudentCount'
        ));
    }

    /**
     * Student joins an event.
     */
    public function join(InterSchoolEvent $interSchoolEvent)
    {
        $student = auth()->guard('student')->user();
        $schoolId = $student->school_id;

        // Check if school has joined
        $schoolParticipation = DB::table('inter_school_event_school')
                                ->where('inter_school_event_id', $interSchoolEvent->id)
                                ->where('school_id', $schoolId)
                                ->where('status', 'joined')
                                ->first();

        if (!$schoolParticipation) {
            return back()->with('error', 'Your school has not joined this event.');
        }

        // Check if students are allowed to join
        if (!$schoolParticipation->can_students_join) {
            return back()->with('error', 'Your school has restricted student participation for this event.');
        }

        // Check class restrictions
        if ($schoolParticipation->allowed_classes) {
            $allowedClasses = json_decode($schoolParticipation->allowed_classes, true);
            if (!in_array($student->class_id, $allowedClasses)) {
                return back()->with('error', 'Your class is not eligible to participate in this event.');
            }
        }

        // Check if already joined
        $exists = DB::table('inter_school_event_student')
                    ->where('inter_school_event_id', $interSchoolEvent->id)
                    ->where('student_id', $student->id)
                    ->exists();

        if ($exists) {
            return back()->with('error', 'You have already joined this event.');
        }

        // Check max participants limit
        if ($interSchoolEvent->max_participants) {
            $currentCount = $interSchoolEvent->students()->count();
            if ($currentCount >= $interSchoolEvent->max_participants) {
                return back()->with('error', 'This event has reached its maximum participant limit.');
            }
        }

        // Join the event
        $interSchoolEvent->students()->attach($student->id, [
            'school_id' => $schoolId,
            'status' => 'joined',
            'approved_by_school' => !$schoolParticipation->manual_approval_required, // Auto-approve if not required
            'joined_at' => now(),
        ]);

        $message = $schoolParticipation->manual_approval_required
                    ? 'Successfully joined! Waiting for school approval.'
                    : 'Successfully joined the event!';

        return redirect()->route('student.inter-school-events.show', $interSchoolEvent)
                        ->with('success', $message);
    }

    /**
     * Display events the student has joined.
     */
    public function myEvents()
    {
        $student = auth()->guard('student')->user();

        $events = InterSchoolEvent::whereHas('students', function ($query) use ($student) {
                        $query->where('student_id', $student->id);
                    })
                    ->with(['students' => function ($query) use ($student) {
                        $query->where('student_id', $student->id)
                              ->withPivot(['status', 'approved_by_school', 'joined_at']);
                    }])
                    ->latest('start_date')
                    ->get();

        return view('student.inter-school-events.my-events', compact('events'));
    }
}
