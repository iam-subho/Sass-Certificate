@extends('layouts.app')

@section('title', 'Pending Certificate Approvals')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Certificate Approvals</h1>
            <p class="mt-1 text-sm text-gray-600">Review and approve certificates issued by teachers/staff</p>
        </div>
        <div class="flex space-x-3">
            <!-- Bulk Approve Button (hidden by default) -->
            <button type="button" id="bulkApproveBtn" class="hidden inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bulk Approve (<span id="selectedCountApprove">0</span>)
            </button>
            <!-- Bulk Reject Button (hidden by default) -->
            <button type="button" id="bulkRejectBtn" class="hidden inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Bulk Reject (<span id="selectedCountReject">0</span>)
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('approvals.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Certificate ID Search -->
            <div>
                <label for="certificate_id" class="block text-sm font-medium text-gray-700 mb-1">Certificate ID</label>
                <input type="text" name="certificate_id" id="certificate_id" value="{{ request('certificate_id') }}" placeholder="Search by ID"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Issuer Filter -->
            <div>
                <label for="issuer_id" class="block text-sm font-medium text-gray-700 mb-1">Issued By</label>
                <select name="issuer_id" id="issuer_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Issuers</option>
                    @foreach($issuers as $issuer)
                        <option value="{{ $issuer->id }}" {{ request('issuer_id') == $issuer->id ? 'selected' : '' }}>
                            {{ $issuer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Event Filter -->
            <div>
                <label for="event_id" class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                <select name="event_id" id="event_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    Apply Filters
                </button>
                <a href="{{ route('approvals.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                    Clear
                </a>
            </div>
        </form>
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

    @if($pendingCertificates->count() > 0)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pendingCertificates as $certificate)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="certificate-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ $certificate->id }}" data-certificate-id="{{ $certificate->certificate_id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">{{ $certificate->certificate_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                            {{ substr($certificate->student->first_name, 0, 1) }}{{ substr($certificate->student->last_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $certificate->student->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $certificate->student->email ?? $certificate->student->mobile }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $certificate->event->name ?? 'N/A' }}</div>
                                @if($certificate->event)
                                    <div class="text-xs text-gray-500">{{ $certificate->event->event_type }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $certificate->rank ?? 'Participation' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $certificate->issuer->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $certificate->issued_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <form action="{{ route('approvals.approve', $certificate) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this certificate?')">
                                        <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Approve
                                    </button>
                                </form>

                                <form action="{{ route('approvals.reject', $certificate) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Reject this certificate?')">
                                        <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Reject
                                    </button>
                                </form>

                                <a href="{{ route('certificates.show', $certificate) }}" class="text-blue-600 hover:text-blue-900">
                                    <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $pendingCertificates->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-white shadow rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['certificate_id', 'issuer_id', 'event_id']))
                    No certificates match your filters. <a href="{{ route('approvals.index') }}" class="text-blue-600 hover:text-blue-900">Clear filters</a>
                @else
                    All certificates have been reviewed.
                @endif
            </p>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const certificateCheckboxes = document.querySelectorAll('.certificate-checkbox');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');
    const selectedCountApprove = document.getElementById('selectedCountApprove');
    const selectedCountReject = document.getElementById('selectedCountReject');

    // Update bulk action buttons visibility and count
    function updateBulkButtons() {
        const selectedCheckboxes = document.querySelectorAll('.certificate-checkbox:checked');
        const count = selectedCheckboxes.length;

        selectedCountApprove.textContent = count;
        selectedCountReject.textContent = count;

        if (count > 0) {
            bulkApproveBtn.classList.remove('hidden');
            bulkApproveBtn.classList.add('inline-flex');
            bulkRejectBtn.classList.remove('hidden');
            bulkRejectBtn.classList.add('inline-flex');
        } else {
            bulkApproveBtn.classList.add('hidden');
            bulkApproveBtn.classList.remove('inline-flex');
            bulkRejectBtn.classList.add('hidden');
            bulkRejectBtn.classList.remove('inline-flex');
        }
    }

    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            certificateCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkButtons();
        });
    }

    // Individual checkbox change
    certificateCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkButtons();

            // Update select all checkbox state
            const allChecked = Array.from(certificateCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(certificateCheckboxes).some(cb => cb.checked);

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Bulk Approve functionality
    if (bulkApproveBtn) {
        bulkApproveBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.certificate-checkbox:checked');
            const certificateIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (certificateIds.length === 0) {
                alert('Please select at least one certificate');
                return;
            }

            if (!confirm(`Are you sure you want to approve ${certificateIds.length} certificate(s)?`)) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("approvals.bulk-approve") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            certificateIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'certificate_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    }

    // Bulk Reject functionality
    if (bulkRejectBtn) {
        bulkRejectBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.certificate-checkbox:checked');
            const certificateIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (certificateIds.length === 0) {
                alert('Please select at least one certificate');
                return;
            }

            if (!confirm(`Are you sure you want to reject ${certificateIds.length} certificate(s)? This action cannot be undone.`)) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("approvals.bulk-reject") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            certificateIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'certificate_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    }

    // Initial update
    updateBulkButtons();
});
</script>
@endpush

@endsection
