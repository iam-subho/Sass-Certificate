@extends('layouts.app')

@section('title', 'Preview Template')

@section('content')
<div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Template Preview: {{ $template->name }}</h1>
        <a href="{{ route('templates.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Back to Templates
        </a>
    </div>

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    This is a preview with sample data. Actual certificates will use real student and school data.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <style>
                /* Ensure scrolling works properly - override template CSS */
                html, body {
                    overflow-y: auto !important;
                    overflow-x: auto !important;
                }

                /* Ensure the preview container is scrollable */
                #template-preview-container {
                    overflow: visible !important;
                    position: relative !important;
                }

                /* Override template's overflow hidden */
                #template-preview-container body,
                #template-preview-container .certificate-page {
                    overflow: visible !important;
                    position: static !important;
                }
            </style>
            <div id="template-preview-container" class="overflow-auto">
                {!! $html !!}
            </div>
        </div>
    </div>
</div>
@endsection
