@extends('layouts.app')

@section('title', 'Create School')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create School</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('schools.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">School Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="certificate_template_id" class="block text-sm font-medium text-gray-700">Certificate Template *</label>
                    <select name="certificate_template_id" id="certificate_template_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Template</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('certificate_template_id') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('certificate_template_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="package_id" class="block text-sm font-medium text-gray-700">Subscription Package</label>
                    <select name="package_id" id="package_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Package (Optional)</option>
                        @foreach($packages as $package)
                        <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                            {{ $package->name }} - ₹{{ number_format($package->price, 2) }} ({{ number_format($package->monthly_certificate_limit) }} certificates/month)
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Assign a subscription package to this school</p>
                    @error('package_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Branding</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700">School Logo</label>
                        <input type="file" name="logo" id="logo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div>
                        <label for="certificate_left_logo" class="block text-sm font-medium text-gray-700">Certificate Left Logo</label>
                        <input type="file" name="certificate_left_logo" id="certificate_left_logo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div>
                        <label for="certificate_right_logo" class="block text-sm font-medium text-gray-700">Certificate Right Logo</label>
                        <input type="file" name="certificate_right_logo" id="certificate_right_logo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Signatures</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="signature_left" class="block text-sm font-medium text-gray-700">Left Signature</label>
                        <input type="file" name="signature_left" id="signature_left" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <input type="text" name="signature_left_title" placeholder="Title (e.g., Principal)" value="{{ old('signature_left_title') }}"
                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label for="signature_middle" class="block text-sm font-medium text-gray-700">Middle Signature</label>
                        <input type="file" name="signature_middle" id="signature_middle" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <input type="text" name="signature_middle_title" placeholder="Title (e.g., Director)" value="{{ old('signature_middle_title') }}"
                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label for="signature_right" class="block text-sm font-medium text-gray-700">Right Signature</label>
                        <input type="file" name="signature_right" id="signature_right" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <input type="text" name="signature_right_title" placeholder="Title (e.g., Chairman)" value="{{ old('signature_right_title') }}"
                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">School Admin Account</h3>
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                    <p class="text-sm text-blue-700">Create an admin account for this school. They will be able to manage students, certificates, and issuers.</p>
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="admin_name" class="block text-sm font-medium text-gray-700">Admin Name *</label>
                        <input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('admin_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700">Admin Email *</label>
                        <input type="email" name="admin_email" id="admin_email" value="{{ old('admin_email') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('admin_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_password" class="block text-sm font-medium text-gray-700">Admin Password *</label>
                        <input type="password" name="admin_password" id="admin_password" required minlength="8"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                        @error('admin_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('schools.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Create School & Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
