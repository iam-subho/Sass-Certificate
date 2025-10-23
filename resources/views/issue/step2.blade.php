@extends('layouts.app')

@section('title', 'Issue Certificates - Step 2')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold">✓</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-green-600">Step 1</div>
                    <div class="text-xs text-gray-500">Class Selected</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-blue-600"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold">2</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-blue-600">Step 2</div>
                    <div class="text-xs text-gray-500">Select Event</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full font-semibold">3</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Step 3</div>
                    <div class="text-xs text-gray-400">Select Students</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full font-semibold">4</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Step 4</div>
                    <div class="text-xs text-gray-400">Delivery</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Step 2: Select Event/Competition</h1>
        <p class="text-sm text-gray-600 mb-6">Selected {{ $students->count() }} students from {{ $class->name }}</p>

        <form action="{{ route('issue.step3') }}" method="POST">
            @csrf

            <div class="space-y-4">
                @forelse($events as $event)
                    <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                        <input type="radio" name="event_id" value="{{ $event->id }}" class="mt-1 text-blue-600 focus:ring-blue-500" required>
                        <div class="ml-4 flex-1">
                            <div class="font-semibold text-gray-900">{{ $event->name }}</div>
                            @if($event->description)
                                <div class="text-sm text-gray-600 mt-1">{{ $event->description }}</div>
                            @endif
                            <div class="flex items-center mt-2 text-xs text-gray-500 space-x-4">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ ucfirst($event->event_type) }}
                                </span>
                                @if($event->event_date)
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $event->event_date->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </label>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No events available</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new event.</p>
                        <div class="mt-6">
                            <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Create Event
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($events->count() > 0)
                <div class="mt-6 flex justify-between">
                    <a href="{{ route('issue.step1') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        ← Back
                    </a>
                    <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Next Step →
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Selected Students Summary -->
    <div class="mt-6 bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Selected Students ({{ $students->count() }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($students as $student)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-semibold">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                        @if($student->section)
                            <div class="text-xs text-gray-500">Section: {{ $student->section }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
