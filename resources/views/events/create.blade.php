@extends('layouts.app')

@section('title', 'Create Event')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Event/Competition</h1>
        <p class="mt-1 text-sm text-gray-600">Add a new event or competition for certificate issuance</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('events.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Event Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Event Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Brief description of the event</p>
                </div>

                <!-- Event Type -->
                <div>
                    <label for="event_type" class="block text-sm font-medium text-gray-700">
                        Event Type <span class="text-red-500">*</span>
                    </label>
                    <select name="event_type" id="event_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('event_type') border-red-500 @enderror">
                        <option value="competition" {{ old('event_type') == 'competition' ? 'selected' : '' }}>Competition</option>
                        <option value="sports" {{ old('event_type') == 'sports' ? 'selected' : '' }}>Sports</option>
                        <option value="cultural" {{ old('event_type') == 'cultural' ? 'selected' : '' }}>Cultural</option>
                        <option value="academic" {{ old('event_type') == 'academic' ? 'selected' : '' }}>Academic</option>
                        <option value="workshop" {{ old('event_type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="seminar" {{ old('event_type') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                        <option value="other" {{ old('event_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('event_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Event Date -->
                <div>
                    <label for="event_date" class="block text-sm font-medium text-gray-700">
                        Event Date
                    </label>
                    <input type="date" name="event_date" id="event_date" value="{{ old('event_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('event_date') border-red-500 @enderror">
                    @error('event_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Certificate Template -->
                <div>
                    <label for="certificate_template_id" class="block text-sm font-medium text-gray-700">
                        Certificate Template
                    </label>
                    <select name="certificate_template_id" id="certificate_template_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('certificate_template_id') border-red-500 @enderror">
                        <option value="">-- Default Template --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ old('certificate_template_id') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('certificate_template_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Select a specific template for this event (optional)</p>
                </div>

                <!-- Is Active -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active (Event is available for certificate issuance)</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('events.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Create Event
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
