@extends('layouts.app')

@section('title', 'Edit Template')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Certificate Template</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('templates.update', $template) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Template Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $template->name) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $template->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="is_active" id="is_active"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="1" {{ old('is_active', $template->is_active) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !old('is_active', $template->is_active) ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label for="html_content" class="block text-sm font-medium text-gray-700 mb-2">HTML Content *</label>
                <div class="mb-2 bg-blue-50 border border-blue-200 rounded-md p-4">
                    <p class="text-sm text-blue-800 font-medium mb-2">Available Placeholders:</p>
                    <div class="grid grid-cols-3 gap-2 text-xs text-blue-700">
                        <code>@{{ first_name }}</code>
                        <code>@{{ last_name }}</code>
                        <code>@{{ full_name }}</code>
                        <code>@{{ dob }}</code>
                        <code>@{{ father_name }}</code>
                        <code>@{{ mother_name }}</code>
                        <code>@{{ mobile }}</code>
                        <code>@{{ email }}</code>
                        <code>@{{ school_name }}</code>
                        <code>@{{ school_email }}</code>
                        <code>@{{ school_phone }}</code>
                        <code>@{{ certificate_id }}</code>
                        <code>@{{ issued_date }}</code>
                        <code>@{{ qr_code }}</code>
                        <code>@{{ certificate_left_logo }}</code>
                        <code>@{{ certificate_right_logo }}</code>
                        <code>@{{ signature_left }}</code>
                        <code>@{{ signature_middle }}</code>
                        <code>@{{ signature_right }}</code>
                        <code>@{{ signature_left_title }}</code>
                        <code>@{{ signature_middle_title }}</code>
                        <code>@{{ signature_right_title }}</code>
                    </div>
                </div>
                <textarea name="html_content" id="html_content" rows="20" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">{{ old('html_content', $template->html_content) }}</textarea>
                @error('html_content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Use HTML with TailwindCSS classes. Use placeholders for dynamic content.</p>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('templates.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
