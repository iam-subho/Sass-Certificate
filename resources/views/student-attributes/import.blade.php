@extends('layouts.app')

@section('title', 'Bulk Import Attributes')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Bulk Import Student Attributes</h1>
        <p class="mt-1 text-sm text-gray-500">Upload a CSV file to assign attributes to multiple students at once.</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
    @endif

    @if(session('import_errors'))
    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
        <h4 class="font-semibold mb-2">Import Warnings:</h4>
        <ul class="list-disc list-inside text-sm max-h-40 overflow-y-auto">
            @foreach(session('import_errors') as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-semibold text-blue-800 mb-2">Instructions</h3>
        <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
            <li>Download the CSV template using the button below</li>
            <li>The template includes all active students and available attributes</li>
            <li>Fill in the values for each attribute column:
                <ul class="list-disc list-inside ml-4 mt-1">
                    <li>For <strong>Enum</strong> attributes: Enter the exact option label (e.g., "Excellent", "Good")</li>
                    <li>For <strong>Numeric</strong> attributes: Enter a number within the specified range</li>
                </ul>
            </li>
            <li>Leave cells empty if you don't want to assign that attribute</li>
            <li>The <code>assigned_at</code> column should contain the date in YYYY-MM-DD format</li>
            <li>Upload the completed CSV file</li>
        </ol>
    </div>

    <!-- Download Template -->
    <div class="bg-white shadow sm:rounded-lg mb-6 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Step 1: Download Template</h3>
        <p class="text-sm text-gray-500 mb-4">Download a CSV template pre-filled with your students and attribute columns.</p>
        <a href="{{ route('student-attributes.import.template') }}"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Template
        </a>
    </div>

    <!-- Available Attributes Reference -->
    <div class="bg-white shadow sm:rounded-lg mb-6 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Attributes</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Attribute</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valid Values</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($attributes as $attr)
                    <tr>
                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $attr->name }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $attr->type->value === 'enum' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $attr->type->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-500">
                            @if($attr->isEnumType())
                                @foreach($attr->options as $opt)
                                    <span class="inline-block bg-gray-100 rounded px-2 py-0.5 mr-1 mb-1">{{ $opt->label }}</span>
                                @endforeach
                            @else
                                {{ $attr->min_value }} - {{ $attr->max_value }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Step 2: Upload Completed CSV</h3>
        <form action="{{ route('student-attributes.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700">CSV File *</label>
                <input type="file" name="file" id="file" accept=".csv,.txt" required
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-xs text-gray-500">Maximum file size: 2MB</p>
                @error('file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('student-attributes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
