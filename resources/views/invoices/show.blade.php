@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Invoice Details</h1>
        <div class="flex gap-2">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to List
            </a>
            @if($invoice->status !== 'paid' and auth()->user()->isSuperAdmin())
            <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Mark as Paid
            </a>
            @endif
            <a href="{{ route('invoices.download', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700" target="_blank">
                Download PDF
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Invoice {{ $invoice->invoice_number }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Invoice details and payment information
            </p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">School</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $invoice->school->name }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Invoice Month</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ \Carbon\Carbon::parse($invoice->month)->format('F Y') }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Plan Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $invoice->plan_type }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Certificate Count</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $invoice->certificates_count }} certificates
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                    <dd class="mt-1 text-lg font-bold text-gray-900 sm:mt-0 sm:col-span-2">
                        ₹{{ number_format($invoice->amount, 2) }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($invoice->status === 'paid')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                        @elseif($invoice->status === 'pending')
                            @if($invoice->due_date < now())
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Overdue</span>
                            @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        @elseif($invoice->status === 'cancelled')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Cancelled</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $invoice->due_date->format('d F Y') }}
                        @if($invoice->status === 'pending' && $invoice->due_date < now())
                        <span class="ml-2 text-red-600 font-semibold">({{ $invoice->due_date->diffForHumans() }})</span>
                        @endif
                    </dd>
                </div>
                @if($invoice->paid_date)
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Paid Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $invoice->paid_date->format('d F Y') }}
                    </dd>
                </div>
                @endif
                @if($invoice->payment_method)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $invoice->payment_method }}
                    </dd>
                </div>
                @endif
                @if($invoice->transaction_id)
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Transaction Id</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $invoice->transaction_id }}
                        </dd>
                    </div>
                @endif
                @if($invoice->notes)
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $invoice->notes }}
                    </dd>
                </div>
                @endif
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Generated On</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $invoice->created_at->format('d F Y, h:i A') }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
