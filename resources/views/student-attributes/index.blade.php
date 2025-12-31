@extends('layouts.app')

@section('title', 'Student Attributes')

@section('content')
<div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Student Attributes</h1>
        <div class="flex space-x-3">
            <a href="{{ route('student-attributes.import.form') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                Bulk Import
            </a>
            <a href="{{ route('student-attributes.import.template') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Download Template
            </a>
        </div>
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

    <!-- Filters -->
    <div class="bg-white shadow sm:rounded-lg mb-6 p-4">
        <form method="GET" action="{{ route('student-attributes.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700">Search Student</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    placeholder="Name or email">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Search
                </button>
                <a href="{{ route('student-attributes.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Available Attributes Summary -->
    <div class="bg-white shadow sm:rounded-lg mb-6 p-4">
        <h3 class="text-sm font-medium text-gray-700 mb-3">Available Attributes</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($attributes as $attr)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attr->type->value === 'enum' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                {{ $attr->name }}
                @if($attr->isGlobal())
                <span class="ml-1 text-gray-400">(Global)</span>
                @endif
            </span>
            @endforeach
        </div>
    </div>

    <!-- Students List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recent Attributes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($students as $student)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $student->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $student->class->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($student->studentAttributes->count() > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach($student->studentAttributes->take(3) as $sa)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800" title="{{ $sa->attribute->name }}: {{ $sa->display_value }}">
                                {{ Str::limit($sa->attribute->name, 10) }}: {{ Str::limit($sa->display_value, 8) }}
                            </span>
                            @endforeach
                            @if($student->studentAttributes->count() > 3)
                            <span class="text-xs text-gray-400">+{{ $student->studentAttributes->count() - 3 }} more</span>
                            @endif
                        </div>
                        @else
                        <span class="text-sm text-gray-400">No attributes assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('student-attributes.create', $student) }}" class="text-blue-600 hover:text-blue-900 mr-3">Assign</a>
                        <a href="{{ route('student-attributes.history', $student) }}" class="text-indigo-600 hover:text-indigo-900">History</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No students found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $students->withQueryString()->links() }}
    </div>
</div>
@endsection
