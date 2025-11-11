@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                <p class="mt-1 text-sm text-gray-600">Created by {{ $event->creator->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('inter-school-events.edit', $event) }}"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Edit Event
                </a>
                @if($event->status === 'draft')
                    <form action="{{ route('inter-school-events.publish', $event) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                            onclick="return confirm('Publish this event and invite all schools?')">
                            Publish Event
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Total Schools Invited</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_schools_invited'] }}</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-green-600">Schools Joined</div>
            <div class="mt-2 text-3xl font-bold text-green-600">{{ $stats['schools_joined'] }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-yellow-600">Schools Pending</div>
            <div class="mt-2 text-3xl font-bold text-yellow-600">{{ $stats['schools_pending'] }}</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-red-600">Schools Rejected</div>
            <div class="mt-2 text-3xl font-bold text-red-600">{{ $stats['schools_rejected'] }}</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-blue-600">Total Students</div>
            <div class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['total_students'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Event Details -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">
                            @if($event->status === 'draft')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @elseif($event->status === 'published')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Published
                                </span>
                            @elseif($event->status === 'ongoing')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Ongoing
                                </span>
                            @elseif($event->status === 'completed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Completed
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Cancelled
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Start Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $event->start_date->format('F d, Y h:i A') }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">End Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $event->end_date->format('F d, Y h:i A') }}</p>
                    </div>

                    @if($event->venue)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Venue</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $event->venue }}</p>
                    </div>
                    @endif

                    @if($event->max_participants)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Max Participants</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $event->max_participants }}</p>
                    </div>
                    @endif

                    @if($event->event_category)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Category</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $event->event_category }}</p>
                    </div>
                    @endif

                    @if($event->description)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $event->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Participating Schools -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Participating Schools</h2>
                    <a href="{{ route('inter-school-events.schools', $event) }}"
                        class="text-sm text-blue-600 hover:text-blue-700">
                        View All →
                    </a>
                </div>

                @if($event->schools->count() > 0)
                    <div class="space-y-3">
                        @foreach($event->schools->take(10) as $school)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $school->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $school->students_count }} students joined</p>
                                </div>
                                <div>
                                    @if($school->pivot->status === 'joined')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Joined
                                        </span>
                                    @elseif($school->pivot->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500">No schools invited yet</p>
                    </div>
                @endif
            </div>

            <!-- Recent Students -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Student Registrations</h2>
                    <a href="{{ route('inter-school-events.students', $event) }}"
                        class="text-sm text-blue-600 hover:text-blue-700">
                        View All →
                    </a>
                </div>

                @if($event->students->count() > 0)
                    <div class="space-y-2">
                        @foreach($event->students->take(5) as $student)
                            <div class="flex items-center justify-between p-2 border-b border-gray-100">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $student->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $student->school->name }}</p>
                                </div>
                                <p class="text-xs text-gray-400">
                                    {{ $student->pivot->joined_at ? \Carbon\Carbon::parse($student->pivot->joined_at)->diffForHumans() : 'N/A' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500">No students registered yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
