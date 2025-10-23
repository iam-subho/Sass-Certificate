@extends('layouts.app')

@section('title', 'Import Students')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Import Students from CSV</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">CSV Format Instructions</h2>
            <p class="text-sm text-gray-600 mb-4">Your CSV file should have the following columns in this exact order:</p>
            <div class="bg-gray-50 p-4 rounded-md">
                <code class="text-sm text-gray-800">
                    first_name, last_name, dob, father_name, mother_name, mobile, email, school_id
                </code>
            </div>
            <p class="text-sm text-gray-600 mt-2">
                <strong>Note:</strong>
                @if(auth()->user()->isSchoolAdmin())
                    As a School Admin, the school_id column is optional (will be auto-filled with your school).
                @else
                    Make sure to include valid school IDs for each student.
                @endif
            </p>
            <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                <p class="text-sm text-blue-700">
                    <strong>Example CSV:</strong><br>
                    John,Doe,2005-05-15,Robert Doe,Jane Doe,+1234567890,john@example.com,1<br>
                    Jane,Smith,2006-08-20,Michael Smith,Sarah Smith,+1234567891,jane@example.com,1
                </p>
            </div>
        </div>

        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="csv_file" class="block text-sm font-medium text-gray-700">Select CSV File *</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" required
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('csv_file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Maximum file size: 5MB</p>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('students.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Import Students
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
