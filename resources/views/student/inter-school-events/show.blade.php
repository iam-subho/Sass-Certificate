@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                <p class="mt-1 text-sm text-gray-600">Inter-School Event</p>
            </div>
            <a href="{{ route('student.inter-school-events.index') }}"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Back to Events
            </a>
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

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Status Banner -->
    @if($hasJoined)
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        @if($approvedBySchool)
                            You have successfully joined this event!
                        @else
                            You have joined this event. Waiting for school approval.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @elseif(!$canJoin)
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Your class is not eligible to participate in this event.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                @if($event->event_category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mb-4">
                        {{ $event->event_category }}
                    </span>
                @endif

                @if($event->description)
                    <p class="text-gray-700 mb-6">{{ $event->description }}</p>
                @endif

                <div class="grid grid-cols-2 gap-4">
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
                    @if($schoolStudentCount > 0)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Students from Your School</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $schoolStudentCount }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Join Button -->
    @if(!$hasJoined && $canJoin && $schoolParticipation->can_students_join)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Ready to participate?</h3>
                <p class="text-sm text-gray-600 mb-4">Join this event to compete with students from other schools!</p>

                <form action="{{ route('student.inter-school-events.join', $event) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700"
                        onclick="return confirm('Are you sure you want to join this event?')">
                        Join Event Now
                    </button>
                </form>

                @if($schoolParticipation->manual_approval_required)
                    <p class="mt-2 text-xs text-gray-500">Note: School approval will be required after joining</p>
                @endif
            </div>
        </div>
    @elseif($hasJoined)
        <div class="bg-blue-50 rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-blue-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">You're Participating!</h3>
            <p class="text-sm text-gray-600">
                @if(!$approvedBySchool)
                    Your participation is pending approval from your school.
                @else
                    Good luck with your participation in this event!
                @endif
            </p>
        </div>
    @elseif(!$canJoin)
        <div class="bg-gray-50 rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Not Eligible</h3>
            <p class="text-sm text-gray-600">Your class is not eligible to participate in this event.</p>
        </div>
    @elseif(!$schoolParticipation->can_students_join)
        <div class="bg-gray-50 rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Student Join Restricted</h3>
            <p class="text-sm text-gray-600">Your school has restricted student participation for this event.</p>
        </div>
    @endif
</div>
@endsection
