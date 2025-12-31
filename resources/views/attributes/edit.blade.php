@extends('layouts.app')

@section('title', 'Edit Attribute')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="attributeForm()">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Global Attribute</h1>
        <p class="mt-1 text-sm text-gray-500">Editing: {{ $attribute->name }}</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('attributes.update', $attribute) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Attribute Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $attribute->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $attribute->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <div class="mt-1 px-3 py-2 bg-gray-100 rounded-md text-sm text-gray-700">
                        {{ $attribute->type->label() }}
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Type cannot be changed after creation</p>
                </div>

                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="is_active" id="is_active" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1" {{ old('is_active', $attribute->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $attribute->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($attribute->isNumericType())
                <!-- Numeric Type Fields -->
                <div>
                    <label for="min_value" class="block text-sm font-medium text-gray-700">Minimum Value *</label>
                    <input type="number" name="min_value" id="min_value" value="{{ old('min_value', $attribute->min_value) }}" step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('min_value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_value" class="block text-sm font-medium text-gray-700">Maximum Value *</label>
                    <input type="number" name="max_value" id="max_value" value="{{ old('max_value', $attribute->max_value) }}" step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif
            </div>

            @if($attribute->isEnumType())
            <!-- Enum Options -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Options *</label>
                <p class="text-xs text-gray-500 mb-3">Options in use cannot be deleted.</p>

                <div class="space-y-3">
                    <template x-for="(option, index) in options" :key="index">
                        <div class="flex items-center space-x-3">
                            <input type="hidden" :name="'options[' + index + '][id]'" x-model="option.id">
                            <div class="flex-1">
                                <input type="text" :name="'options[' + index + '][label]'" x-model="option.label"
                                    placeholder="Label (e.g., Excellent)"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="w-32">
                                <input type="number" :name="'options[' + index + '][numeric_value]'" x-model="option.numeric_value"
                                    placeholder="Score" step="0.01"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <button type="button" @click="removeOption(index)" x-show="options.length > 1 && !option.id"
                                class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <span x-show="option.id" class="text-xs text-gray-400">Saved</span>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addOption()"
                    class="mt-3 inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Option
                </button>

                @error('options')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('attributes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update Attribute
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function attributeForm() {
    return {
        options: @json($attribute->options->map(fn($o) => ['id' => $o->id, 'label' => $o->label, 'numeric_value' => $o->numeric_value])),
        addOption() {
            this.options.push({ id: null, label: '', numeric_value: 0 });
        },
        removeOption(index) {
            if (!this.options[index].id) {
                this.options.splice(index, 1);
            }
        }
    }
}
</script>
@endsection
