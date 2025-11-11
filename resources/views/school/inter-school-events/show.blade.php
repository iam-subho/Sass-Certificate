@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showJoinModal: false }">
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                <p class="mt-1 text-sm text-gray-600">Inter-School Event</p>
            </div>
            <a href="{{ route('school.inter-school-events.index') }}"
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
    @if($participation && $participation->pivot->status === 'joined')
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        Your school has joined this event. {{ $students->count() }} students have registered so far.
                    </p>
                </div>
            </div>
        </div>
    @elseif($participation && $participation->pivot->status === 'rejected')
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">Your school has rejected this event invitation.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Event Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h2>

                @if($event->event_category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mb-4">
                        {{ $event->event_category }}
                    </span>
                @endif

                @if($event->description)
                    <p class="text-gray-700 mb-4">{{ $event->description }}</p>
                @endif

                <div class="grid grid-cols-2 gap-4 mt-4">
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
                </div>
            </div>

            <!-- Students List (if joined) -->
            @if($participation && $participation->pivot->status === 'joined' && $students->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Registered Students</h2>
                        <a href="{{ route('school.inter-school-events.manage-students', $event) }}"
                            class="text-sm text-blue-600 hover:text-blue-700">
                            Manage Students →
                        </a>
                    </div>

                    <div class="space-y-2">
                        @foreach($students->take(5) as $student)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $student->full_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $student->class ? $student->class->name : 'N/A' }}
                                        @if($student->section) - {{ $student->section }} @endif
                                    </p>
                                </div>
                                @if($student->pivot->approved_by_school)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>

                @if(!$participation || $participation->pivot->status === 'pending')
                    <button type="button" @click.stop="showJoinModal = true"
                        class="w-full mb-3 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                        Join Event
                    </button>

                    <form action="{{ route('school.inter-school-events.reject', $event) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700"
                            onclick="return confirm('Are you sure you want to reject this event?')">
                            Reject Event
                        </button>
                    </form>
                @elseif($participation && $participation->pivot->status === 'joined')
                    <a href="{{ route('school.inter-school-events.manage-students', $event) }}"
                        class="block w-full mb-3 text-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        Manage Students
                    </a>

                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Current Settings</h3>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>Student Join: {{ $participation->pivot->can_students_join ? 'Allowed' : 'Restricted' }}</li>
                            <li>Manual Approval: {{ $participation->pivot->manual_approval_required ? 'Required' : 'Not Required' }}</li>
                            @if($participation->pivot->allowed_classes)
                                <li>Class restrictions applied</li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Join Modal -->
    <div x-show="showJoinModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click="showJoinModal = false">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Modal Content -->
        <div @click.stop
             class="relative bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">

            <form action="{{ route('school.inter-school-events.join', $event) }}" method="POST">
                @csrf

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Configure Event Participation</h3>
                </div>

                <!-- Body -->
                <div class="px-6 py-4 space-y-6">

                    <!-- Can Students Join -->
                    <div>
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox"
                                   name="can_students_join"
                                   value="1"
                                   checked
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Allow students to join freely</span>
                                <span class="block text-xs text-gray-500 mt-1">If disabled, students cannot join this event</span>
                            </div>
                        </label>
                    </div>

                    <!-- Manual Approval -->
                    <div>
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox"
                                   name="manual_approval_required"
                                   value="1"
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Require manual approval for students</span>
                                <span class="block text-xs text-gray-500 mt-1">You will need to approve each student manually</span>
                            </div>
                        </label>
                    </div>

                    <!-- Class Restrictions -->
                    @if(count($classes) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Restrict to specific classes (optional)
                        </label>
                        <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-md bg-white">
                            @foreach($classes as $class)
                                <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <input type="checkbox"
                                           name="allowed_classes[]"
                                           value="{{ $class->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    <span class="ml-2 text-sm text-gray-700">{{ $class->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Select multiple classes or leave empty for all classes</p>
                    </div>
                    @endif

                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button"
                            @click="showJoinModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Join Event
                    </button>
                </div>

            </form>

        </div>
    </div>



</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
