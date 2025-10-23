@extends('layouts.app')

@section('title', 'Add Student')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add Student</h1>
        <p class="mt-1 text-sm text-gray-600">Fill in student details to add a new student record</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('students.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="dob" class="block text-sm font-medium text-gray-700">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="dob" id="dob" value="{{ old('dob') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('dob') border-red-500 @enderror">
                    @error('dob')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mobile -->
                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700">
                        Mobile <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}" required
                        placeholder="e.g., 9876543210"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('mobile') border-red-500 @enderror">
                    @error('mobile')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Father's Name -->
                <div>
                    <label for="father_name" class="block text-sm font-medium text-gray-700">
                        Father's Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="father_name" id="father_name" value="{{ old('father_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('father_name') border-red-500 @enderror">
                    @error('father_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mother's Name -->
                <div>
                    <label for="mother_name" class="block text-sm font-medium text-gray-700">
                        Mother's Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('mother_name') border-red-500 @enderror">
                    @error('mother_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email (Optional)</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        placeholder="student@example.com"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- School -->
                <div>
                    <label for="school_id" class="block text-sm font-medium text-gray-700">
                        School <span class="text-red-500">*</span>
                    </label>
                    <select name="school_id" id="school_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('school_id') border-red-500 @enderror"
                        {{ auth()->user()->isSchoolAdmin() ? 'readonly' : '' }}>
                        <option value="">-- Select School --</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class (Optional)</label>
                    <select name="class_id" id="class_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('class_id') border-red-500 @enderror">
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Section -->
                <div>
                    <label for="section" class="block text-sm font-medium text-gray-700">Section (Optional)</label>
                    <select name="section" id="section"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('section') border-red-500 @enderror"
                        disabled>
                        <option value="">-- Select Class First --</option>
                    </select>
                    @error('section')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Select a class to load sections</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Student
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const sectionSelect = document.getElementById('section');
    const oldSection = '{{ old("section") }}';

    classSelect.addEventListener('change', function() {
        const classId = this.value;

        // Reset section dropdown
        sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
        sectionSelect.disabled = true;

        if (!classId) {
            sectionSelect.innerHTML = '<option value="">-- Select Class First --</option>';
            return;
        }

        // Fetch sections for the selected class
        fetch(`/api/classes/${classId}/sections`)
            .then(response => response.json())
            .then(data => {
                if (data.sections && data.sections.length > 0) {
                    sectionSelect.disabled = false;
                    data.sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section;
                        option.textContent = section;
                        if (oldSection === section) {
                            option.selected = true;
                        }
                        sectionSelect.appendChild(option);
                    });
                } else {
                    sectionSelect.innerHTML = '<option value="">-- No Sections Available --</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching sections:', error);
                sectionSelect.innerHTML = '<option value="">-- Error Loading Sections --</option>';
            });
    });

    // Trigger change event if class is already selected (e.g., after validation error)
    if (classSelect.value) {
        classSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush

@endsection
