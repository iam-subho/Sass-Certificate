@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Students</h1>
            <p class="mt-1 text-sm text-gray-600">Manage student records</p>
        </div>
        <div class="space-x-3">
            <a href="{{ route('students.import.form') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Import CSV
            </a>
            <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Student
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

    <div class="bg-white shadow sm:rounded-lg relative">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOB</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class/Section</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login Status</th>
                    @if(auth()->user()->isSuperAdmin())
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $student->father_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $student->dob->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($student->class)
                            <div class="text-sm text-gray-900">{{ $student->class->name }}</div>
                            @if($student->section)
                                <div class="text-xs text-gray-500">Section: {{ $student->section }}</div>
                            @endif
                        @else
                            <span class="text-sm text-gray-400">Not assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $student->mobile }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $student->email ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            @if($student->username && $student->password)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Has Login
                                </span>
                                <div class="text-xs text-gray-500">@ {{ $student->username }}</div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    No Login
                                </span>
                            @endif
                            @if($student->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $student->school->name }}
                        </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium relative" x-data="{ open: false }">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('students.edit', $student) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <div class="relative">
                                <button @click="open = !open" x-ref="button" class="text-gray-600 hover:text-gray-900" title="More actions">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>
                            </div>

                            <div x-show="open" @click.away="open = false" x-cloak class="fixed w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 :style="`top: ${$refs.button.getBoundingClientRect().bottom + 8}px; right: ${window.innerWidth - $refs.button.getBoundingClientRect().left}px;`"
                                 x-ref="menu">
                                <div class="py-1">
                                    @if(!$student->username || !$student->password)
                                        <form action="{{ route('students.generate-credentials', $student) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-gray-100">
                                                Generate Login Credentials
                                            </button>
                                        </form>
                                    @endif

                                    @if($student->username && $student->password && $student->email)
                                        <form action="{{ route('students.send-credentials', $student) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-gray-100">
                                                Send Credentials via Email
                                            </button>
                                        </form>
                                    @endif

                                    @if($student->username && $student->password)
                                        <form action="{{ route('students.reset-password', $student) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-yellow-700 hover:bg-gray-100" onclick="return confirm('Reset password to default (DOB)?')">
                                                Reset Password
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('students.toggle-active', $student) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm {{ $student->is_active ? 'text-orange-700' : 'text-green-700' }} hover:bg-gray-100">
                                            {{ $student->is_active ? 'Deactivate Account' : 'Activate Account' }}
                                        </button>
                                    </form>

                                    @if($student->username)
                                        <a href="{{ route('profile.public', $student->username) }}" target="_blank" class="block px-4 py-2 text-sm text-purple-700 hover:bg-gray-100">
                                            View Public Profile
                                        </a>
                                    @endif

                                    <div class="border-t border-gray-100"></div>

                                    <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100">
                                            Delete Student
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isSuperAdmin() ? '8' : '7' }}" class="px-6 py-4 text-center text-sm text-gray-500">
                        No students found. <a href="{{ route('students.create') }}" class="text-blue-600 hover:text-blue-900">Add your first student</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>
@endsection
