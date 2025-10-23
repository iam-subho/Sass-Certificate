@extends('layouts.app')

@section('title', 'Certificate Templates')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Certificate Templates</h1>
        <a href="{{ route('templates.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            Create Template
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($templates as $template)
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-medium text-gray-900">{{ $template->name }}</h3>
                    @if($template->is_active)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                    @endif
                </div>
                @if($template->description)
                <p class="text-sm text-gray-500 mb-4">{{ Str::limit($template->description, 100) }}</p>
                @endif
                <div class="text-sm text-gray-600 mb-4">
                    <span class="font-medium">{{ $template->schools_count }}</span> schools using this template
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('templates.preview', $template) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                        Preview
                    </a>
                    <a href="{{ route('templates.edit', $template) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700">
                        Edit
                    </a>
                    <form action="{{ route('templates.toggle-status', $template) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-yellow-600 hover:bg-yellow-700">
                            {{ $template->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    <form action="{{ route('templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <p class="text-gray-500">No templates found. <a href="{{ route('templates.create') }}" class="text-blue-600 hover:text-blue-900">Create your first template</a></p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $templates->links() }}
    </div>
</div>
@endsection
