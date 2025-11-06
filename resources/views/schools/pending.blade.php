@extends('layouts.app')

@section('title', 'Pending School Approvals')

@section('content')
<div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Pending School Approvals</h1>
            <p class="mt-1 text-sm text-gray-600">Review and approve new school registrations</p>
        </div>
        <a href="{{ route('schools.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to All Schools
        </a>
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

    @if($pendingSchools->count() > 0)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @foreach($pendingSchools as $school)
                <div class="border-b border-gray-200 hover:bg-gray-50 p-6">
                    <div class="flex items-start justify-between">
                        <!-- School Details -->
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <h3 class="text-xl font-bold text-gray-900">{{ $school->name }}</h3>
                                <span class="ml-3 px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending Approval
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <!-- Contact Information -->
                                <div>
                                    <h4 class="font-semibold text-gray-700 mb-2">Contact Information</h4>
                                    <dl class="space-y-1">
                                        @if($school->email)
                                            <div class="flex items-center">
                                                <dt class="text-gray-500 w-24">Email:</dt>
                                                <dd class="text-gray-900">{{ $school->email }}</dd>
                                            </div>
                                        @endif
                                        @if($school->phone)
                                            <div class="flex items-center">
                                                <dt class="text-gray-500 w-24">Phone:</dt>
                                                <dd class="text-gray-900">{{ $school->phone }}</dd>
                                            </div>
                                        @endif
                                        @if($school->address)
                                            <div class="flex">
                                                <dt class="text-gray-500 w-24">Address:</dt>
                                                <dd class="text-gray-900 flex-1">{{ $school->address }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>

                                <!-- School Admin -->
                                <div>
                                    <h4 class="font-semibold text-gray-700 mb-2">School Administrator</h4>
                                    <dl class="space-y-1">
                                        <div class="flex items-center">
                                            <dt class="text-gray-500 w-24">Name:</dt>
                                            <dd class="text-gray-900">{{ $school->admin->name ?? 'N/A' }}</dd>
                                        </div>
                                        <div class="flex items-center">
                                            <dt class="text-gray-500 w-24">Email:</dt>
                                            <dd class="text-gray-900">{{ $school->admin->email ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Subscription Details -->
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-2 text-sm">Subscription Details</h4>
                                <div class="flex items-center space-x-6 text-sm">
                                    <div>
                                        <span class="text-gray-500">Plan:</span>
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full
                                            @if($school->plan_type == 'free') bg-gray-100 text-gray-800
                                            @elseif($school->plan_type == 'basic') bg-blue-100 text-blue-800
                                            @elseif($school->plan_type == 'premium') bg-purple-100 text-purple-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($school->plan_type) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Monthly Limit:</span>
                                        <span class="ml-2 font-semibold text-gray-900">{{ number_format($school->monthly_certificate_limit) }} certificates</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Registered:</span>
                                        <span class="ml-2 text-gray-900">{{ $school->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="ml-6 flex flex-col space-y-2">
                            <form action="{{ route('schools.approve', $school) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to approve this school?');">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Approve
                                </button>
                            </form>

                            <form action="{{ route('schools.reject', $school) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject this school? This action cannot be undone.');">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Reject
                                </button>
                            </form>

                            <a href="{{ route('schools.show', $school) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $pendingSchools->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-white shadow rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
            <p class="mt-1 text-sm text-gray-500">All schools have been reviewed.</p>
            <div class="mt-6">
                <a href="{{ route('schools.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    View All Schools
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
