<?php

namespace App\Http\Controllers;

use App\Models\InterSchoolEvent;
use App\Models\School;
use Illuminate\Http\Request;

class InterSchoolEventController extends Controller
{
    /**
     * Display a listing of inter-school events.
     */
    public function index()
    {
        $events = InterSchoolEvent::with(['creator:id,name'])
                    ->withCount(['schools', 'students'])
                    ->latest()
                    ->paginate(20);

        return view('inter-school-events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('inter-school-events.create');
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'event_category' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        $validated['created_by'] = auth()->id();

        $event = InterSchoolEvent::create($validated);

        // If published, invite all active schools
        if ($validated['status'] === 'published') {
            $schools = School::where('is_active', true)
                            ->where('status', 'approved')
                            ->pluck('id');

            foreach ($schools as $schoolId) {
                $event->schools()->attach($schoolId, [
                    'status' => 'pending',
                    'can_students_join' => true,
                    'manual_approval_required' => false,
                ]);
            }
        }

        return redirect()->route('inter-school-events.index')
                        ->with('success', 'Inter-school event created successfully.');
    }

    /**
     * Display the specified event with participating schools and students.
     */
    public function show(InterSchoolEvent $interSchoolEvent)
    {
        $event = $interSchoolEvent->load([
            'creator:id,name',
            'schools' => function ($query) {
                $query->withPivot(['status', 'can_students_join', 'allowed_classes', 'manual_approval_required', 'joined_at', 'rejected_at'])
                      ->withCount('students');
            },
            'students.school:id,name'
        ]);

        $stats = [
            'total_schools_invited' => $event->schools()->count(),
            'schools_joined' => $event->schools()->wherePivot('status', 'joined')->count(),
            'schools_pending' => $event->schools()->wherePivot('status', 'pending')->count(),
            'schools_rejected' => $event->schools()->wherePivot('status', 'rejected')->count(),
            'total_students' => $event->students()->count(),
        ];

        return view('inter-school-events.show', compact('event', 'stats'));
    }

    /**
     * Show the form for editing the event.
     */
    public function edit(InterSchoolEvent $interSchoolEvent)
    {
        return view('inter-school-events.edit', compact('interSchoolEvent'));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, InterSchoolEvent $interSchoolEvent)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'event_category' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,ongoing,completed,cancelled',
        ]);

        $interSchoolEvent->update($validated);

        // If changing from draft to published, invite all schools
        if ($interSchoolEvent->wasChanged('status') && $validated['status'] === 'published') {
            $schools = School::where('is_active', true)
                            ->where('status', 'approved')
                            ->pluck('id');

            foreach ($schools as $schoolId) {
                // Only attach if not already invited
                if (!$interSchoolEvent->schools()->where('school_id', $schoolId)->exists()) {
                    $interSchoolEvent->schools()->attach($schoolId, [
                        'status' => 'pending',
                        'can_students_join' => true,
                        'manual_approval_required' => false,
                    ]);
                }
            }
        }

        return redirect()->route('inter-school-events.index')
                        ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(InterSchoolEvent $interSchoolEvent)
    {
        $interSchoolEvent->delete();

        return redirect()->route('inter-school-events.index')
                        ->with('success', 'Event deleted successfully.');
    }

    /**
     * Show schools participating in this event.
     */
    public function schools(InterSchoolEvent $interSchoolEvent)
    {
        $schools = $interSchoolEvent->schools()
                    ->withPivot(['status', 'can_students_join', 'allowed_classes', 'manual_approval_required', 'joined_at', 'rejected_at'])
                    ->withCount('students')
                    ->paginate(20);

        return view('inter-school-events.schools', compact('interSchoolEvent', 'schools'));
    }

    /**
     * Show students participating in this event.
     */
    public function students(InterSchoolEvent $interSchoolEvent)
    {
        $students = $interSchoolEvent->students()
                    ->with(['school:id,name', 'class:id,name'])
                    ->withPivot(['status', 'school_id', 'approved_by_school', 'joined_at'])
                    ->paginate(50);

        return view('inter-school-events.students', compact('interSchoolEvent', 'students'));
    }

    /**
     * Publish event (change status to published).
     */
    public function publish(InterSchoolEvent $interSchoolEvent)
    {
        if ($interSchoolEvent->status !== 'draft') {
            return back()->with('error', 'Only draft events can be published.');
        }

        $interSchoolEvent->update(['status' => 'published']);

        // Invite all active schools
        $schools = School::where('is_active', true)
                        ->where('status', 'approved')
                        ->pluck('id');

        foreach ($schools as $schoolId) {
            if (!$interSchoolEvent->schools()->where('school_id', $schoolId)->exists()) {
                $interSchoolEvent->schools()->attach($schoolId, [
                    'status' => 'pending',
                    'can_students_join' => true,
                    'manual_approval_required' => false,
                ]);
            }
        }

        return back()->with('success', 'Event published successfully and invitations sent to all schools.');
    }
}
