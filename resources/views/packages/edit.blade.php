@extends('layouts.app')

@section('title', 'Edit Package')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Package</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('packages.update', $package) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Package Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., Basic, Premium, Enterprise">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Describe the features and benefits of this package">{{ old('description', $package->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="monthly_certificate_limit" class="block text-sm font-medium text-gray-700">Monthly Certificate Limit *</label>
                    <input type="number" name="monthly_certificate_limit" id="monthly_certificate_limit"
                        value="{{ old('monthly_certificate_limit', $package->monthly_certificate_limit) }}" required min="1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., 100">
                    <p class="mt-1 text-xs text-gray-500">Maximum certificates that can be issued per month</p>
                    @error('monthly_certificate_limit')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_months" class="block text-sm font-medium text-gray-700">Duration (Months) *</label>
                    <input type="number" name="duration_months" id="duration_months"
                        value="{{ old('duration_months', $package->duration_months) }}" required min="1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., 12">
                    <p class="mt-1 text-xs text-gray-500">How long the package lasts</p>
                    @error('duration_months')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price (₹) *</label>
                    <input type="number" name="price" id="price"
                        value="{{ old('price', $package->price) }}" required min="0" step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., 5000.00">
                    @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="is_active" id="is_active" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1" {{ old('is_active', $package->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $package->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Only active packages can be assigned to schools</p>
                    @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($package->schools()->count() > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            This package is currently assigned to {{ $package->schools()->count() }} {{ Str::plural('school', $package->schools()->count()) }}. Changes to certificate limits will affect these schools.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('packages.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update Package
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
