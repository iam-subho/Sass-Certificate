@extends('layouts.app')

@section('title', 'Generate Certificate')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Generate Certificate</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('certificates.generate') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700">Select Student *</label>
                <select name="student_id" id="student_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Choose a student</option>
                    @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                        {{ $student->full_name }}
                        @if(auth()->user()->isSuperAdmin())
                            - {{ $student->school->name }}
                        @endif
                        ({{ $student->dob->format('d M Y') }})
                    </option>
                    @endforeach
                </select>
                @error('student_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Select the student for whom you want to generate a certificate</p>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            The certificate will be generated with a unique ID and QR code. The student's school must have a certificate template assigned.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('certificates.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Generate Certificate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
