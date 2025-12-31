@extends('layouts.app')

@section('title', 'Assign Attribute')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="assignForm()">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Assign Attribute</h1>
        <p class="mt-1 text-sm text-gray-500">Assigning to: <strong>{{ $student->full_name }}</strong></p>
    </div>

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('student-attributes.store', $student) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="attribute_id" class="block text-sm font-medium text-gray-700">Attribute *</label>
                    <select name="attribute_id" id="attribute_id" required x-model="selectedAttribute" @change="updateAttribute()"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select an attribute</option>
                        @foreach($attributes as $attr)
                        <option value="{{ $attr->id }}" data-type="{{ $attr->type->value }}" data-min="{{ $attr->min_value }}" data-max="{{ $attr->max_value }}" data-options='@json($attr->options)'>
                            {{ $attr->name }} ({{ $attr->type->label() }})
                            @if($attr->isGlobal()) - Global @endif
                        </option>
                        @endforeach
                    </select>
                    @error('attribute_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="assigned_at" class="block text-sm font-medium text-gray-700">Assignment Date *</label>
                    <input type="date" name="assigned_at" id="assigned_at" value="{{ old('assigned_at', date('Y-m-d')) }}" required
                        max="{{ date('Y-m-d') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('assigned_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden input to submit the actual value -->
                <input type="hidden" name="value" x-model="selectedValue">

                <!-- Enum Value Selection -->
                <div x-show="attributeType === 'enum'" x-transition>
                    <label for="value_enum" class="block text-sm font-medium text-gray-700">Value *</label>
                    <select id="value_enum" x-model="selectedValue"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select an option</option>
                        <template x-for="opt in options" :key="opt.id">
                            <option :value="opt.id" x-text="opt.label + ' (' + opt.numeric_value + ')'"></option>
                        </template>
                    </select>
                    @error('value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Numeric Value Input -->
                <div x-show="attributeType === 'numeric'" x-transition>
                    <label for="value_numeric" class="block text-sm font-medium text-gray-700">Value *</label>
                    <input type="number" id="value_numeric" step="0.01" x-model="selectedValue"
                        x-bind:min="minValue" x-bind:max="maxValue"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500" x-show="minValue !== null || maxValue !== null">
                        Range: <span x-text="minValue ?? 'N/A'"></span> - <span x-text="maxValue ?? 'N/A'"></span>
                    </p>
                    @error('value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Optional notes about this assignment">{{ old('remarks') }}</textarea>
                    @error('remarks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('student-attributes.history', $student) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Assign Attribute
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function assignForm() {
    return {
        selectedAttribute: '',
        attributeType: '',
        selectedValue: '',
        options: [],
        minValue: null,
        maxValue: null,
        updateAttribute() {
            const select = document.getElementById('attribute_id');
            const option = select.options[select.selectedIndex];

            // Reset value when attribute changes
            this.selectedValue = '';

            if (option && option.value) {
                this.attributeType = option.dataset.type;
                this.minValue = option.dataset.min ? parseFloat(option.dataset.min) : null;
                this.maxValue = option.dataset.max ? parseFloat(option.dataset.max) : null;
                this.options = JSON.parse(option.dataset.options || '[]');
            } else {
                this.attributeType = '';
                this.options = [];
                this.minValue = null;
                this.maxValue = null;
            }
        }
    }
}
</script>
@endsection
