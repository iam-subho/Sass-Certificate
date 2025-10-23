@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Certificate issuance statistics and insights</p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Certificates -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Certificates</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_certificates']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_month']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Approvals</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_approvals']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Active Events -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Events</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_events']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Certificates by Event -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificates by Event</h3>
            @if($certificatesByEvent->count() > 0)
                <div class="space-y-3">
                    @foreach($certificatesByEvent as $item)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $item->event_name }}</span>
                                <span class="text-gray-900 font-semibold">{{ $item->count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($item->count / $stats['total_certificates']) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No data available</p>
            @endif
        </div>

        <!-- Certificates by Month -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trend (Last 6 Months)</h3>
            @if($certificatesByMonth->count() > 0)
                <div class="space-y-3">
                    @foreach($certificatesByMonth as $item)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $item->month }}</span>
                                <span class="text-gray-900 font-semibold">{{ $item->count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($item->count / $certificatesByMonth->max('count')) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No data available</p>
            @endif
        </div>
    </div>

    <!-- Additional Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Certificates by Class -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificates by Class</h3>
            @if($certificatesByClass->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($certificatesByClass as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $item->class_name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ $item->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No data available</p>
            @endif
        </div>

        <!-- Top Students -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Students (Most Certificates)</h3>
            @if($topStudents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($topStudents as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $item->student_name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ $item->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No data available</p>
            @endif
        </div>
    </div>

    <!-- Monthly Limit Progress -->
    @if(auth()->user()->isSchoolAdmin())
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Certificate Limit</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="font-medium text-gray-700">Progress</span>
                    <span class="text-gray-900">
                        {{ number_format(auth()->user()->school->certificates_issued_this_month) }} / {{ number_format(auth()->user()->school->monthly_certificate_limit) }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    @php
                        $percentage = (auth()->user()->school->certificates_issued_this_month / auth()->user()->school->monthly_certificate_limit) * 100;
                        $colorClass = $percentage >= 90 ? 'bg-red-600' : ($percentage >= 70 ? 'bg-yellow-600' : 'bg-green-600');
                    @endphp
                    <div class="{{ $colorClass }} h-3 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500">
                    <span>{{ number_format($percentage, 1) }}% used</span>
                    <span>{{ number_format(auth()->user()->school->monthly_certificate_limit - auth()->user()->school->certificates_issued_this_month) }} remaining</span>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
