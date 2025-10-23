@extends('layouts.app')

@section('title', 'School Profile')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">School Profile</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your school information and logos</p>
    </div>

    <form method="POST" action="{{ route('school.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Basic Information Card -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Basic Information(You can not edit this section)</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">School contact and identification details</p>
            </div>
            <div class="px-4 py-5 sm:p-6 space-y-4">
                <!-- School Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        School Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $school->name) }}"
                        required
                        readonly
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        readonly
                        value="{{ old('email', $school->email) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="phone"
                        id="phone"
                        readonly
                        value="{{ old('phone', $school->phone) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-500 @enderror"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Logos Card -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">School Logos(You can edit this section)</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Upload school logos for general use and certificates</p>
            </div>
            <div class="px-4 py-5 sm:p-6 space-y-6">
                <!-- Main School Logo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Main School Logo
                    </label>
                    @if($school->logo)
                        <div class="mb-3 flex items-start space-x-4">
                            <img src="{{ asset('storage/' . $school->logo) }}" alt="School Logo" class="h-24 w-24 object-contain border border-gray-300 rounded">
                            <button
                                type="button"
                                onclick="deleteImage('logo')"
                                class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                            >
                                Delete
                            </button>
                        </div>
                    @endif
                    <input
                        type="file"
                        name="logo"
                        id="logo"
                        accept="image/jpeg,image/png,image/jpg"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG. Max size: 2MB</p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Certificate Left Logo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Certificate Left Logo
                    </label>
                    @if($school->certificate_left_logo)
                        <div class="mb-3 flex items-start space-x-4">
                            <img src="{{ asset('storage/' . $school->certificate_left_logo) }}" alt="Certificate Left Logo" class="h-24 w-24 object-contain border border-gray-300 rounded">
                            <button
                                type="button"
                                onclick="deleteImage('certificate_left_logo')"
                                class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                            >
                                Delete
                            </button>
                        </div>
                    @endif
                    <input
                        type="file"
                        name="certificate_left_logo"
                        id="certificate_left_logo"
                        accept="image/jpeg,image/png,image/jpg"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG. Max size: 2MB</p>
                    @error('certificate_left_logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Certificate Right Logo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Certificate Right Logo
                    </label>
                    @if($school->certificate_right_logo)
                        <div class="mb-3 flex items-start space-x-4">
                            <img src="{{ asset('storage/' . $school->certificate_right_logo) }}" alt="Certificate Right Logo" class="h-24 w-24 object-contain border border-gray-300 rounded">
                            <button
                                type="button"
                                onclick="deleteImage('certificate_right_logo')"
                                class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                            >
                                Delete
                            </button>
                        </div>
                    @endif
                    <input
                        type="file"
                        name="certificate_right_logo"
                        id="certificate_right_logo"
                        accept="image/jpeg,image/png,image/jpg"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG. Max size: 2MB</p>
                    @error('certificate_right_logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Signatures Card -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Certificate Signatures</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Upload signature images and titles for certificates</p>
            </div>
            <div class="px-4 py-5 sm:p-6 space-y-8">
                <!-- Left Signature -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Left Signature</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Signature Image
                            </label>
                            @if($school->signature_left)
                                <div class="mb-3 flex items-start space-x-4">
                                    <img src="{{ asset('storage/' . $school->signature_left) }}" alt="Left Signature" class="h-24 w-auto object-contain border border-gray-300 rounded">
                                    <button
                                        type="button"
                                        onclick="deleteImage('signature_left')"
                                        class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                                    >
                                        Delete
                                    </button>
                                </div>
                            @endif
                            <input
                                type="file"
                                name="signature_left"
                                id="signature_left"
                                accept="image/jpeg,image/png,image/jpg"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            >
                            <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG. Max size: 2MB</p>
                            @error('signature_left')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="signature_left_title" class="block text-sm font-medium text-gray-700">
                                Title (e.g., Principal, Coordinator)
                            </label>
                            <input
                                type="text"
                                name="signature_left_title"
                                id="signature_left_title"
                                value="{{ old('signature_left_title', $school->signature_left_title) }}"
                                placeholder="Principal"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                        </div>
                    </div>
                </div>

                <!-- Middle Signature -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Middle Signature</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Signature Image
                            </label>
                            @if($school->signature_middle)
                                <div class="mb-3 flex items-start space-x-4">
                                    <img src="{{ asset('storage/' . $school->signature_middle) }}" alt="Middle Signature" class="h-24 w-auto object-contain border border-gray-300 rounded">
                                    <button
                                        type="button"
                                        onclick="deleteImage('signature_middle')"
                                        class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                                    >
                                        Delete
                                    </button>
                                </div>
                            @endif
                            <input
                                type="file"
                                name="signature_middle"
                                id="signature_middle"
                                accept="image/jpeg,image/png,image/jpg"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            >
                            <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG. Max size: 2MB</p>
                            @error('signature_middle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="signature_middle_title" class="block text-sm font-medium text-gray-700">
                                Title (e.g., Vice Principal, HOD)
                            </label>
                            <input
                                type="text"
                                name="signature_middle_title"
                                id="signature_middle_title"
                                value="{{ old('signature_middle_title', $school->signature_middle_title) }}"
                                placeholder="Vice Principal"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                        </div>
                    </div>
                </div>

                <!-- Right Signature -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 mb-4">Right Signature</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Signature Image
                            </label>
                            @if($school->signature_right)
                                <div class="mb-3 flex items-start space-x-4">
                                    <img src="{{ asset('storage/' . $school->signature_right) }}" alt="Right Signature" class="h-24 w-auto object-contain border border-gray-300 rounded">
                                    <button
                                        type="button"
                                        onclick="deleteImage('signature_right')"
                                        class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                                    >
                                        Delete
                                    </button>
                                </div>
                            @endif
                            <input
                                type="file"
                                name="signature_right"
                                id="signature_right"
                                accept="image/jpeg,image/png,image/jpg"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            >
                            <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG. Max size: 2MB</p>
                            @error('signature_right')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="signature_right_title" class="block text-sm font-medium text-gray-700">
                                Title (e.g., Coordinator, Dean)
                            </label>
                            <input
                                type="text"
                                name="signature_right_title"
                                id="signature_right_title"
                                value="{{ old('signature_right_title', $school->signature_right_title) }}"
                                placeholder="Coordinator"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Profile
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function deleteImage(field) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('school.profile.delete-image') }}';

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';

    const fieldInput = document.createElement('input');
    fieldInput.type = 'hidden';
    fieldInput.name = 'field';
    fieldInput.value = field;

    form.appendChild(csrfToken);
    form.appendChild(fieldInput);

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
