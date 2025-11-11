@extends('layouts.app')

@section('title', 'Super Admin Analytics')

@push('styles')
<style>
    /* FullCalendar minimal styles */
    .fc { position: relative; }
    .fc-header-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .fc-toolbar-chunk { display: flex; gap: 0.5rem; }
    .fc-daygrid { border: 1px solid #e5e7eb; }
    .fc-col-header { background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    .fc-scrollgrid { border-collapse: collapse; width: 100%; }
    .fc-scrollgrid td, .fc-scrollgrid th { border: 1px solid #e5e7eb; }
    .fc-daygrid-day { min-height: 100px; }
    .fc-daygrid-day-frame { padding: 4px; min-height: 100px; }
    .fc-daygrid-day-number { padding: 4px; }
    .fc-daygrid-day-top { display: flex; justify-content: flex-end; }
    .fc-event { margin: 2px; padding: 2px 4px; border-radius: 3px; font-size: 0.875rem; cursor: pointer; }
    .fc-day-today { background-color: #eef2ff !important; }
</style>
@endpush

@section('content')
<div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Super Admin Analytics</h1>
        <p class="mt-1 text-sm text-gray-600">System-wide statistics and insights</p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Schools -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-medium text-gray-500 truncate">Total Schools</dt>
                        <dd class="text-xl font-bold text-gray-900">{{ number_format($stats['total_schools']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Active Schools -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-medium text-gray-500 truncate">Active Schools</dt>
                        <dd class="text-xl font-bold text-gray-900">{{ number_format($stats['active_schools']) }}</dd>
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
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-medium text-gray-500 truncate">Pending</dt>
                        <dd class="text-xl font-bold text-gray-900">{{ number_format($stats['pending_schools']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Certificates -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-medium text-gray-500 truncate">Certificates</dt>
                        <dd class="text-xl font-bold text-gray-900">{{ number_format($stats['total_certificates']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-medium text-gray-500 truncate">This Month</dt>
                        <dd class="text-xl font-bold text-gray-900">{{ number_format($stats['this_month_certificates']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="mb-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Inter-School Events Calendar</h3>
                <div class="flex gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded" style="background-color: #8B5CF6;"></div>
                        <span class="text-gray-600">Inter-School Events</span>
                    </div>
                </div>
            </div>
            <div id="fullcalendar" data-events-url="{{ route('api.calendar.events.super') }}"></div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Schools by Certificates -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Schools (Most Certificates)</h3>
            @if($topSchools->count() > 0)
                <div class="space-y-3">
                    @foreach($topSchools as $item)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $item->school_name }}</span>
                                <span class="text-gray-900 font-semibold">{{ $item->count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($item->count / $topSchools->first()->count) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No data available</p>
            @endif
        </div>

        <!-- Monthly Certificate Trend -->
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

    <!-- Revenue & Plans -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue by Plan -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Schools by Plan Type</h3>
            @if($schoolsByPlan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Schools</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($schoolsByPlan as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($item->plan_type == 'free') bg-gray-100 text-gray-800
                                            @elseif($item->plan_type == 'basic') bg-blue-100 text-blue-800
                                            @elseif($item->plan_type == 'premium') bg-purple-100 text-purple-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($item->plan_type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right font-semibold">{{ $item->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No data available</p>
            @endif
        </div>

        <!-- Recent Invoices -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Invoices</h3>
                <span class="text-sm text-gray-500">Total Revenue: ₹{{ number_format($stats['total_revenue'], 2) }}</span>
            </div>
            @if($recentInvoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">School</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentInvoices as $invoice)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $invoice->school->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">₹{{ number_format($invoice->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($invoice->status == 'paid') bg-green-100 text-green-800
                                            @elseif($invoice->status == 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No invoices found</p>
            @endif
        </div>
    </div>

    <!-- System Health -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">System Health & Activity</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Approval Rate -->
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-3xl font-bold text-green-600">
                    {{ $stats['active_schools'] > 0 ? number_format(($stats['active_schools'] / $stats['total_schools']) * 100, 1) : 0 }}%
                </div>
                <div class="text-sm text-gray-600 mt-1">School Approval Rate</div>
            </div>

            <!-- Average Certificates per School -->
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-600">
                    {{ $stats['active_schools'] > 0 ? number_format($stats['total_certificates'] / $stats['active_schools'], 1) : 0 }}
                </div>
                <div class="text-sm text-gray-600 mt-1">Avg. Certificates per School</div>
            </div>

            <!-- Monthly Growth -->
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-3xl font-bold text-purple-600">
                    {{ number_format($stats['this_month_certificates']) }}
                </div>
                <div class="text-sm text-gray-600 mt-1">Certificates This Month</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <h3 class="text-xl font-bold mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('schools.pending') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-center transition">
                <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="font-semibold">Approve Schools</div>
                <div class="text-sm opacity-90">{{ $stats['pending_schools'] }} pending</div>
            </a>
            <a href="{{ route('schools.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-center transition">
                <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <div class="font-semibold">Manage Schools</div>
                <div class="text-sm opacity-90">View all schools</div>
            </a>
            <a href="{{ route('certificates.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-center transition">
                <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                <div class="font-semibold">View Certificates</div>
                <div class="text-sm opacity-90">Browse all certificates</div>
            </a>
        </div>
    </div>
</div>
@endsection
