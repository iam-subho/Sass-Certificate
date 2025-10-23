<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $events = Event::with(['school:id,name', 'certificateTemplate:id,name'])->latest()->paginate(20);
        } else {
            $events = Event::where('school_id', $user->school_id)
                ->with('certificateTemplate:id,name')
                ->latest()
                ->paginate(20);
        }

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $user = auth()->user();
        $templates = CertificateTemplate::where('is_active', true)->select('id', 'name', 'description')->get();

        return view('events.create', compact('templates'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $validated['school_id'] = $user->school_id;
        $validated['is_active'] = $request->has('is_active');

        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && $event->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $event->load(['school:id,name', 'certificateTemplate:id,name', 'certificates.student:id,first_name,last_name']);

        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && $event->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $templates = CertificateTemplate::where('is_active', true)->select('id', 'name', 'description')->get();

        return view('events.edit', compact('event', 'templates'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->has('is_active');

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        // Authorization already handled by UpdateEventRequest rules
        $user = auth()->user();

        if (!$user->isSuperAdmin() && $event->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
