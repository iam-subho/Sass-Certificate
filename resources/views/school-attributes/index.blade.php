@extends('layouts.app')

@section('title', 'Attributes')

@section('content')
<div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Attributes</h1>
        <a href="{{ route('school-attributes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            Add Attribute
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
    @endif

    <!-- Global Attributes (Read-only) -->
    @if($globalAttributes->count() > 0)
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Global Attributes</h2>
        <p class="text-sm text-gray-500 mb-4">These attributes are managed by the system administrator and available to all schools.</p>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Options/Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($globalAttributes as $attribute)
                    <tr class="bg-blue-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $attribute->name }}</div>
                            @if($attribute->description)
                            <div class="text-sm text-gray-500">{{ Str::limit($attribute->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $attribute->type->value === 'enum' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $attribute->type->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($attribute->isEnumType())
                                <div class="text-sm text-gray-500">
                                    @foreach($attribute->options->take(3) as $option)
                                        <span class="inline-block bg-gray-100 rounded px-2 py-0.5 mr-1 mb-1">{{ $option->label }}</span>
                                    @endforeach
                                    @if($attribute->options->count() > 3)
                                        <span class="text-gray-400">+{{ $attribute->options->count() - 3 }} more</span>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm text-gray-500">
                                    Range: {{ $attribute->min_value ?? 'N/A' }} - {{ $attribute->max_value ?? 'N/A' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('school-attributes.show', $attribute) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            <span class="text-gray-300 mx-2">|</span>
                            <span class="text-gray-400 text-xs">Read-only</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- School-Specific Attributes -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">School Attributes</h2>
        <p class="text-sm text-gray-500 mb-4">These attributes are specific to your school and can be fully managed by you.</p>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Options/Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schoolAttributes as $attribute)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="{{ route('school-attributes.show', $attribute) }}" class="hover:text-blue-600">{{ $attribute->name }}</a>
                            </div>
                            @if($attribute->description)
                            <div class="text-sm text-gray-500">{{ Str::limit($attribute->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $attribute->type->value === 'enum' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $attribute->type->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($attribute->isEnumType())
                                <div class="text-sm text-gray-500">
                                    @foreach($attribute->options->take(3) as $option)
                                        <span class="inline-block bg-gray-100 rounded px-2 py-0.5 mr-1 mb-1">{{ $option->label }}</span>
                                    @endforeach
                                    @if($attribute->options->count() > 3)
                                        <span class="text-gray-400">+{{ $attribute->options->count() - 3 }} more</span>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm text-gray-500">
                                    Range: {{ $attribute->min_value ?? 'N/A' }} - {{ $attribute->max_value ?? 'N/A' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ number_format($attribute->student_attributes_count) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attribute->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('school-attributes.show', $attribute) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            <a href="{{ route('school-attributes.edit', $attribute) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('school-attributes.toggle-status', $attribute) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    {{ $attribute->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                            @if($attribute->student_attributes_count == 0)
                            <form action="{{ route('school-attributes.destroy', $attribute) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this attribute?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No school-specific attributes found. <a href="{{ route('school-attributes.create') }}" class="text-blue-600 hover:underline">Create one</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $schoolAttributes->links() }}
        </div>
    </div>
</div>
@endsection
