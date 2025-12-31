@extends('layouts.app')

@section('title', 'Edit Assignment')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="editForm()">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Attribute Assignment</h1>
        <p class="mt-1 text-sm text-gray-500">
            Student: <strong>{{ $studentAttribute->student->full_name }}</strong><br>
            Attribute: <strong>{{ $studentAttribute->attribute->name }}</strong>
        </p>
    </div>

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('student-attributes.update', $studentAttribute) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Attribute</label>
                    <div class="mt-1 px-3 py-2 bg-gray-100 rounded-md text-sm text-gray-700">
                        {{ $studentAttribute->attribute->name }}
                        <span class="text-xs text-gray-500">({{ $studentAttribute->attribute->type->label() }})</span>
                    </div>
                </div>

                <div>
                    <label for="assigned_at" class="block text-sm font-medium text-gray-700">Assignment Date *</label>
                    <input type="date" name="assigned_at" id="assigned_at"
                        value="{{ old('assigned_at', $studentAttribute->assigned_at->format('Y-m-d')) }}" required
                        max="{{ date('Y-m-d') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('assigned_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($studentAttribute->attribute->isEnumType())
                <!-- Enum Value Selection -->
                <div class="sm:col-span-2">
                    <label for="value" class="block text-sm font-medium text-gray-700">Value *</label>
                    <select name="value" id="value" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($studentAttribute->attribute->options as $option)
                        <option value="{{ $option->id }}" {{ old('value', $studentAttribute->attribute_option_id) == $option->id ? 'selected' : '' }}>
                            {{ $option->label }} ({{ $option->numeric_value }})
                        </option>
                        @endforeach
                    </select>
                    @error('value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <!-- Numeric Value Input -->
                <div class="sm:col-span-2">
                    <label for="value" class="block text-sm font-medium text-gray-700">Value *</label>
                    <input type="number" name="value" id="value" step="0.01" required
                        value="{{ old('value', $studentAttribute->numeric_value) }}"
                        min="{{ $studentAttribute->attribute->min_value }}"
                        max="{{ $studentAttribute->attribute->max_value }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">
                        Range: {{ $studentAttribute->attribute->min_value ?? 'N/A' }} - {{ $studentAttribute->attribute->max_value ?? 'N/A' }}
                    </p>
                    @error('value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div class="sm:col-span-2">
                    <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Optional notes about this assignment">{{ old('remarks', $studentAttribute->remarks) }}</textarea>
                    @error('remarks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md">
                <p class="text-xs text-gray-500">
                    Originally assigned by <strong>{{ $studentAttribute->assignedByUser->name ?? 'Unknown' }}</strong>
                    on {{ $studentAttribute->created_at->format('M d, Y H:i') }}
                </p>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('student-attributes.history', $studentAttribute->student) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update Assignment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editForm() {
    return {}
}
</script>
@endsection
