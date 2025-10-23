@extends('layouts.app')

@section('title', 'Mark Invoice as Paid')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Update Invoice</h1>
        <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Cancel
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Invoice {{ $invoice->invoice_number }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Amount: ₹{{ number_format($invoice->amount, 2) }} | School: {{ $invoice->school->name }}
            </p>
        </div>

        <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="px-4 py-5 sm:p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pending" {{ old('status', $invoice->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Paid Date -->
                <div id="paid_date_field" style="display: none;">
                    <label for="paid_date" class="block text-sm font-medium text-gray-700">Paid Date</label>
                    <input type="date" name="paid_date" id="paid_date" value="{{ old('paid_date', $invoice->paid_date ? $invoice->paid_date->format('Y-m-d') : now()->format('Y-m-d')) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" max="{{ now()->format('Y-m-d') }}">
                    @error('paid_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div id="payment_method_field" style="display: none;">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="bank_transfer" {{ old('payment_method', $invoice->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="upi" {{ old('payment_method', $invoice->payment_method) == 'upi' ? 'selected' : '' }}>UPI</option>
                        <option value="cash" {{ old('payment_method', $invoice->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                    </select>
                    @error('payment_method')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction ID -->
                <div id="transaction_id_field"  style="display: none;">
                    <label for="transaction_id" class="block text-sm font-medium text-gray-700">Notes</label>
                    <input name="transaction_id" id="transaction_id" value="{{ old('transaction_id', $invoice->transaction_id) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Transaction id..." />
                    @error('transaction_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add any additional notes...">{{ old('notes', $invoice->notes) }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const paidDateField = document.getElementById('paid_date_field');
    const paymentMethodField = document.getElementById('payment_method_field');
    const transactionIdField = document.getElementById('transaction_id_field');

    function togglePaymentFields() {
        if (statusSelect.value === 'paid') {
            paidDateField.style.display = 'block';
            paymentMethodField.style.display = 'block';
            transactionIdField.style.display = 'block';
        } else {
            paidDateField.style.display = 'none';
            paymentMethodField.style.display = 'none';
            transactionIdField.style.display = 'none';
        }
    }

    // Initialize on page load
    togglePaymentFields();

    // Update when status changes
    statusSelect.addEventListener('change', togglePaymentFields);
});
</script>
@endsection
