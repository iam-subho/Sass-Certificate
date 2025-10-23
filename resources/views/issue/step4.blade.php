@extends('layouts.app')

@section('title', 'Issue Certificates - Step 4')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold">✓</div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-green-600"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold">✓</div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-green-600"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold">✓</div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-blue-600"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold">4</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-blue-600">Step 4</div>
                    <div class="text-xs text-gray-500">Delivery Options</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Step 4: Delivery Options & Confirmation</h1>
        <p class="text-sm text-gray-600 mb-6">Review and confirm certificate issuance for <span class="font-semibold">{{ $event->name }}</span></p>

        <form action="{{ route('issue.confirm') }}" method="POST" onsubmit="return confirm('Are you sure you want to generate these certificates? This action cannot be undone.');">
            @csrf

            <!-- Delivery Options -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Options</h3>
                <div class="space-y-4 p-4 bg-gray-50 rounded-lg">
                    <label class="flex items-start">
                        <input type="checkbox" name="send_email" value="1" class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Send via Email</span>
                            <p class="text-xs text-gray-500 mt-1">Certificates will be sent to student's email addresses (if available)</p>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="send_whatsapp" value="1" class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Send via WhatsApp</span>
                            <p class="text-xs text-gray-500 mt-1">Certificates will be sent to student's mobile numbers</p>
                        </div>
                    </label>
                </div>
                <p class="mt-2 text-xs text-gray-500">Note: Delivery options are queued and will be processed in the background. You can download certificates manually from the certificates list.</p>
            </div>

            <!-- Summary -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-700">Event/Competition:</dt>
                            <dd class="text-sm text-gray-900 font-semibold">{{ $event->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-700">Total Students:</dt>
                            <dd class="text-sm text-gray-900 font-semibold">{{ $students->count() }}</dd>
                        </div>
                        @if(auth()->user()->isIssuer())
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-700">Status:</dt>
                                <dd class="text-sm">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending Approval
                                    </span>
                                </dd>
                            </div>
                        @else
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-700">Status:</dt>
                                <dd class="text-sm">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Auto-Approved
                                    </span>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Students List -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Selected Students ({{ $students->count() }})</h3>
                <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $student)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->full_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $student->email ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $student->mobile }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Warnings/Info -->
            @if(auth()->user()->school->certificates_issued_this_month + $students->count() > auth()->user()->school->monthly_certificate_limit)
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Monthly limit exceeded</h3>
                            <p class="text-sm text-red-700 mt-1">Your school has exceeded the monthly certificate limit. Please contact admin to upgrade your plan.</p>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->isIssuer())
                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Approval Required</h3>
                            <p class="text-sm text-yellow-700 mt-1">These certificates will be sent for approval to your School Admin before being finalized.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-between">
                <a href="{{ url()->previous() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    ← Back
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Generate Certificates
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
