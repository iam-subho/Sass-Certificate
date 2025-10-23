@extends('layouts.app')

@section('title', 'School Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">School Details</h1>
        <div class="space-x-3">
            <a href="{{ route('schools.edit', $school) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit School
            </a>
            <a href="{{ route('schools.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    @if(auth()->user()->isSuperAdmin())
    <!-- Status Management (Super Admin Only) -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Status Management</h3>
            <p class="mt-1 text-sm text-gray-600">Manage school approval status</p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-700 mb-2">Current Status:</p>
                    @if($school->status == 'approved')
                        <span class="px-3 py-2 text-sm font-semibold rounded-md bg-green-100 text-green-800">Approved</span>
                    @elseif($school->status == 'pending')
                        <span class="px-3 py-2 text-sm font-semibold rounded-md bg-yellow-100 text-yellow-800">Pending Approval</span>
                    @elseif($school->status == 'rejected')
                        <span class="px-3 py-2 text-sm font-semibold rounded-md bg-red-100 text-red-800">Rejected</span>
                    @elseif($school->status == 'suspended')
                        <span class="px-3 py-2 text-sm font-semibold rounded-md bg-gray-100 text-gray-800">Suspended</span>
                    @endif
                </div>
                <div class="flex space-x-3">
                    @if($school->status != 'approved')
                    <form action="{{ route('schools.approve', $school) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approve
                        </button>
                    </form>
                    @endif
                    @if($school->status != 'rejected')
                    <form action="{{ route('schools.reject', $school) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject this school?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Reject
                        </button>
                    </form>
                    @endif
                    @if($school->status != 'suspended')
                    <form action="{{ route('schools.suspend', $school) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to suspend this school?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                            Suspend
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Invoice Section (Super Admin Only) -->
    @if(auth()->user()->isSuperAdmin())
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Invoice Information</h3>
                <p class="mt-1 text-sm text-gray-600">Billing and payment details</p>
            </div>
            <a href="{{ route('invoices.index') }}?school_id={{ $school->id }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                View All Invoices
            </a>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-900">Total Invoices</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $invoiceStats['total_invoices'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-900">Paid</p>
                            <p class="text-2xl font-bold text-green-600">₹{{ number_format($invoiceStats['total_paid_amount'], 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-yellow-900">Pending</p>
                            <p class="text-2xl font-bold text-yellow-600">₹{{ number_format($invoiceStats['pending_amount'], 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-red-900">Overdue</p>
                            <p class="text-2xl font-bold text-red-600">{{ $invoiceStats['overdue_invoices'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($recentInvoices->count() > 0)
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Recent Invoices</h4>
                <div class="overflow-hidden border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentInvoices as $invoice)
                            <tr>
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ \Carbon\Carbon::parse($invoice->month)->format('M Y') }}</td>
                                <td class="px-4 py-2 text-sm font-semibold text-gray-900">₹{{ number_format($invoice->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm">
                                    @if($invoice->status === 'paid')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                    @elseif($invoice->status === 'pending')
                                        @if($invoice->due_date < now())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Overdue</span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $invoice->due_date->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">No invoices generated yet</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Students</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_students'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Certificates</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_certificates'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['pending_certificates'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['approved_certificates'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Classes</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_classes'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Events</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_events'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- School Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">School Information</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">School Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $school->name }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $school->email }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $school->phone }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Certificate Template</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $school->certificateTemplate->name ?? 'N/A' }}
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Subscription Package</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if($school->package)
                                    <span class="font-semibold">{{ $school->package->name }}</span> - ₹{{ number_format($school->package->price, 2) }} for {{ $school->package->duration_months }} months
                                @else
                                    <span class="text-gray-400">No package assigned</span>
                                @endif
                            </dd>
                        </div>
                        @if($school->plan_start_date || $school->plan_expiry_date)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Plan Duration</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if($school->plan_start_date)
                                    <span>{{ $school->plan_start_date->format('d M Y') }}</span>
                                @endif
                                @if($school->plan_start_date && $school->plan_expiry_date)
                                    <span class="mx-2">→</span>
                                @endif
                                @if($school->plan_expiry_date)
                                    <span>{{ $school->plan_expiry_date->format('d M Y') }}</span>
                                    @if($school->plan_expiry_date < now())
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                    @else
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @endif
                                @endif
                            </dd>
                        </div>
                        @endif
                        @if($school->monthly_certificate_limit)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Certificate Usage</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div class="flex items-center">
                                    <span class="font-semibold">{{ $school->certificates_issued_this_month ?? 0 }} / {{ number_format($school->monthly_certificate_limit) }}</span>
                                    <span class="ml-2 text-gray-500">this month</span>
                                </div>
                                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $percentage = $school->monthly_certificate_limit > 0
                                            ? min(100, ($school->certificates_issued_this_month / $school->monthly_certificate_limit) * 100)
                                            : 0;
                                        $color = $percentage >= 90 ? 'bg-red-500' : ($percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                                    @endphp
                                    <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </dd>
                        </div>
                        @endif
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Active Status</dt>
                            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                @if($school->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Approval Status</dt>
                            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                @if($school->status == 'approved')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                @elseif($school->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($school->status == 'rejected')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                @elseif($school->status == 'suspended')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Suspended</span>
                                @endif
                            </dd>
                        </div>
                        @if($school->approved_at)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Approved On</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $school->approved_at->format('d M Y, h:i A') }}
                                @if($school->approver)
                                    by {{ $school->approver->name }}
                                @endif
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Logo and Status -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">School Logo</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 text-center">
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="max-w-full h-auto mx-auto rounded-lg shadow-md" style="max-height: 200px;">
                    @else
                        <div class="text-gray-400">
                            <svg class="mx-auto h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm">No logo uploaded</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Images -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Certificate Branding</h3>
            <p class="mt-1 text-sm text-gray-600">Images used in certificate generation</p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Logo -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Certificate Left Logo</h4>
                    @if($school->certificate_left_logo)
                        <img src="{{ asset('storage/' . $school->certificate_left_logo) }}" alt="Left Logo" class="h-24 w-auto border border-gray-200 rounded-lg p-2">
                    @else
                        <p class="text-sm text-gray-500">Not uploaded</p>
                    @endif
                </div>

                <!-- Right Logo -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Certificate Right Logo</h4>
                    @if($school->certificate_right_logo)
                        <img src="{{ asset('storage/' . $school->certificate_right_logo) }}" alt="Right Logo" class="h-24 w-auto border border-gray-200 rounded-lg p-2">
                    @else
                        <p class="text-sm text-gray-500">Not uploaded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Signatures</h3>
            <p class="mt-1 text-sm text-gray-600">Authority signatures for certificates</p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Signature -->
                <div class="text-center">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Left Signature</h4>
                    @if($school->signature_left)
                        <img src="{{ asset('storage/' . $school->signature_left) }}" alt="Signature" class="h-16 w-auto mx-auto border border-gray-200 rounded p-2">
                    @else
                        <p class="text-sm text-gray-500">Not uploaded</p>
                    @endif
                    <p class="text-xs text-gray-600 mt-2">{{ $school->signature_left_title ?? 'No title' }}</p>
                </div>

                <!-- Middle Signature -->
                <div class="text-center">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Middle Signature</h4>
                    @if($school->signature_middle)
                        <img src="{{ asset('storage/' . $school->signature_middle) }}" alt="Signature" class="h-16 w-auto mx-auto border border-gray-200 rounded p-2">
                    @else
                        <p class="text-sm text-gray-500">Not uploaded</p>
                    @endif
                    <p class="text-xs text-gray-600 mt-2">{{ $school->signature_middle_title ?? 'No title' }}</p>
                </div>

                <!-- Right Signature -->
                <div class="text-center">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Right Signature</h4>
                    @if($school->signature_right)
                        <img src="{{ asset('storage/' . $school->signature_right) }}" alt="Signature" class="h-16 w-auto mx-auto border border-gray-200 rounded p-2">
                    @else
                        <p class="text-sm text-gray-500">Not uploaded</p>
                    @endif
                    <p class="text-xs text-gray-600 mt-2">{{ $school->signature_right_title ?? 'No title' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Admins and Issuers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- School Admins -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">School Admins</h3>
            </div>
            <div class="border-t border-gray-200">
                @if($school->admins->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($school->admins as $admin)
                        <li class="px-4 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $admin->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $admin->email }}</p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="px-4 py-5">
                        <p class="text-sm text-gray-500">No admins assigned</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Issuers -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Issuers</h3>
            </div>
            <div class="border-t border-gray-200">
                @if($school->issuers->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($school->issuers as $issuer)
                        <li class="px-4 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($issuer->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $issuer->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $issuer->email }}</p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="px-4 py-5">
                        <p class="text-sm text-gray-500">No issuers assigned</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
