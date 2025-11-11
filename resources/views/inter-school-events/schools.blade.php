@extends('layouts.app')

@section('title', 'Participating Schools - ' . $interSchoolEvent->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('inter-school-events.index') }}" class="text-sm text-gray-700 hover:text-blue-600">
                    Inter-School Events
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('inter-school-events.show', $interSchoolEvent) }}" class="ml-1 text-sm text-gray-700 hover:text-blue-600">
                        {{ Str::limit($interSchoolEvent->title, 30) }}
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500">Participating Schools</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Participating Schools</h1>
        <p class="mt-1 text-sm text-gray-600">Schools invited to {{ $interSchoolEvent->title }}</p>
    </div>

    @if($schools->count() > 0)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Settings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schools as $school)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $school->name }}</div>
                                <div class="text-sm text-gray-500">{{ $school->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($school->pivot->status === 'joined')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Joined
                                    </span>
                                @elseif($school->pivot->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $school->students_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($school->pivot->status === 'joined')
                                    <div class="space-y-1">
                                        <div>
                                            <span class="text-xs text-gray-500">Student Join:</span>
                                            <span class="text-xs {{ $school->pivot->can_students_join ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $school->pivot->can_students_join ? 'Allowed' : 'Restricted' }}
                                            </span>
                                        </div>
                                        @if($school->pivot->manual_approval_required)
                                            <div class="text-xs text-blue-600">Manual Approval Required</div>
                                        @endif
                                        @if($school->pivot->allowed_classes)
                                            <div class="text-xs text-gray-500">Class restrictions applied</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($school->pivot->status === 'joined' && $school->pivot->joined_at)
                                    {{ \Carbon\Carbon::parse($school->pivot->joined_at)->format('M d, Y') }}
                                @elseif($school->pivot->status === 'rejected' && $school->pivot->rejected_at)
                                    {{ \Carbon\Carbon::parse($school->pivot->rejected_at)->format('M d, Y') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $schools->links() }}
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No schools invited yet</h3>
            <p class="mt-1 text-sm text-gray-500">Publish the event to invite schools.</p>
        </div>
    @endif
</div>
@endsection
