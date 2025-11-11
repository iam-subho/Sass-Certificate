@extends('layouts.app')

@section('title', 'Edit Inter-School Event')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Inter-School Event</h1>
        <p class="mt-1 text-sm text-gray-600">Update event details</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('inter-school-events.update', $interSchoolEvent) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Event Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $interSchoolEvent->title) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $interSchoolEvent->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="start_date" id="start_date"
                        value="{{ old('start_date', $interSchoolEvent->start_date->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="end_date" id="end_date"
                        value="{{ old('end_date', $interSchoolEvent->end_date->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Venue and Max Participants -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">
                        Venue
                    </label>
                    <input type="text" name="venue" id="venue" value="{{ old('venue', $interSchoolEvent->venue) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('venue') border-red-500 @enderror">
                    @error('venue')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                        Max Participants
                    </label>
                    <input type="number" name="max_participants" id="max_participants"
                        value="{{ old('max_participants', $interSchoolEvent->max_participants) }}" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('max_participants') border-red-500 @enderror">
                    @error('max_participants')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave empty for unlimited participants</p>
                </div>
            </div>

            <!-- Event Category -->
            <div class="mb-6">
                <label for="event_category" class="block text-sm font-medium text-gray-700 mb-2">
                    Event Category
                </label>
                <select name="event_category" id="event_category"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('event_category') border-red-500 @enderror">
                    <option value="">Select category</option>
                    <option value="Sports" {{ old('event_category', $interSchoolEvent->event_category) == 'Sports' ? 'selected' : '' }}>Sports</option>
                    <option value="Cultural" {{ old('event_category', $interSchoolEvent->event_category) == 'Cultural' ? 'selected' : '' }}>Cultural</option>
                    <option value="Academic" {{ old('event_category', $interSchoolEvent->event_category) == 'Academic' ? 'selected' : '' }}>Academic</option>
                    <option value="Science" {{ old('event_category', $interSchoolEvent->event_category) == 'Science' ? 'selected' : '' }}>Science</option>
                    <option value="Technology" {{ old('event_category', $interSchoolEvent->event_category) == 'Technology' ? 'selected' : '' }}>Technology</option>
                    <option value="Arts" {{ old('event_category', $interSchoolEvent->event_category) == 'Arts' ? 'selected' : '' }}>Arts</option>
                    <option value="Competition" {{ old('event_category', $interSchoolEvent->event_category) == 'Competition' ? 'selected' : '' }}>Competition</option>
                    <option value="Other" {{ old('event_category', $interSchoolEvent->event_category) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('event_category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="draft" {{ old('status', $interSchoolEvent->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $interSchoolEvent->status) == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="ongoing" {{ old('status', $interSchoolEvent->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ old('status', $interSchoolEvent->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $interSchoolEvent->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('inter-school-events.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Update Event
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
