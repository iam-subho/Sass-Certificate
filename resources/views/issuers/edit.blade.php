@extends('layouts.app')

@section('title', 'Edit Issuer')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Issuer</h1>
        <p class="mt-1 text-sm text-gray-600">Update issuer information</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('issuers.update', $issuer) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $issuer->name) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $issuer->email) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="is_active" id="is_active"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="1" {{ old('is_active', $issuer->is_active) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !old('is_active', $issuer->is_active) ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Password (Optional) -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password (Optional)</h3>

                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password. Minimum 8 characters if changing.</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Stats Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Issuer Activity</h4>
                <dl class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Total Certificates Issued:</dt>
                        <dd class="font-semibold text-gray-900">{{ $issuer->issuedCertificates()->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Account Created:</dt>
                        <dd class="font-semibold text-gray-900">{{ $issuer->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between">
                <a href="{{ route('issuers.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Update Issuer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
