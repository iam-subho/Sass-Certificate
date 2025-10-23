@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Certificates</h1>
            <p class="mt-1 text-sm text-gray-600">Manage and view all certificates</p>
        </div>
        <div class="flex space-x-3">
            <!-- Bulk Print Button (hidden by default) -->
            <button type="button" id="bulkPrintBtn" class="hidden inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Bulk Print (<span id="selectedCount">0</span>)
            </button>
            @if(auth()->user()->isSchoolAdmin() || auth()->user()->isIssuer())
                <a href="{{ route('issue.step1') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Issue Certificates
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('certificates.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Certificate ID or Student Name"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    Apply Filters
                </button>
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

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    @if(auth()->user()->isSuperAdmin())
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                    @if(!auth()->user()->isIssuer())
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued By</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($certificates as $certificate)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($certificate->status == 'approved')
                            <input type="checkbox" class="certificate-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ $certificate->id }}" data-certificate-id="{{ $certificate->certificate_id }}">
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-blue-600">
                        <a href="{{ route('certificates.show', $certificate) }}" class="hover:underline">
                            {{ $certificate->certificate_id }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $certificate->student->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $certificate->student->mobile }}</div>
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $certificate->school->name }}</td>
                    @endif
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $certificate->event->name ?? 'N/A' }}</div>
                        @if($certificate->event)
                            <div class="text-xs text-gray-500">{{ ucfirst($certificate->event->event_type) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $certificate->rank ?? 'Participation' }}
                        </span>
                    </td>
                    @if(!auth()->user()->isIssuer())
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $certificate->issuer->name ?? 'N/A' }}
                        </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $certificate->issued_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($certificate->status == 'approved')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        @elseif($certificate->status == 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Rejected
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('certificates.show', $certificate) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        @if($certificate->status == 'approved')
                            <a href="{{ route('certificates.download', $certificate) }}" class="text-green-600 hover:text-green-900">Download</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isSuperAdmin() ? '10' : (auth()->user()->isIssuer() ? '8' : '9') }}" class="px-6 py-4 text-center text-sm text-gray-500">
                        No certificates found.
                        @if(auth()->user()->isSchoolAdmin() || auth()->user()->isIssuer())
                            <a href="{{ route('issue.step1') }}" class="text-blue-600 hover:text-blue-900">Issue your first certificate</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $certificates->appends(request()->query())->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const certificateCheckboxes = document.querySelectorAll('.certificate-checkbox');
    const bulkPrintBtn = document.getElementById('bulkPrintBtn');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Update bulk print button visibility and count
    function updateBulkPrintButton() {
        const selectedCheckboxes = document.querySelectorAll('.certificate-checkbox:checked');
        const count = selectedCheckboxes.length;

        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkPrintBtn.classList.remove('hidden');
            bulkPrintBtn.classList.add('inline-flex');
        } else {
            bulkPrintBtn.classList.add('hidden');
            bulkPrintBtn.classList.remove('inline-flex');
        }
    }

    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            certificateCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkPrintButton();
        });
    }

    // Individual checkbox change
    certificateCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkPrintButton();

            // Update select all checkbox state
            const allChecked = Array.from(certificateCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(certificateCheckboxes).some(cb => cb.checked);

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Bulk Print functionality
    if (bulkPrintBtn) {
        bulkPrintBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.certificate-checkbox:checked');
            const certificateIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (certificateIds.length === 0) {
                alert('Please select at least one certificate');
                return;
            }

            // Open in new tab
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("certificates.bulk-print") }}';
            form.target = '_blank';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add certificate IDs
            certificateIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'certificate_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });
    }

    // Initial update
    updateBulkPrintButton();
});
</script>
@endpush

@endsection
