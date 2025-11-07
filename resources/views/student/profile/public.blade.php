<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $student->full_name }} - Student Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $student->full_name }} - Student Profile">
    <meta property="og:description" content="{{ $student->bio ? Str::limit($student->bio, 160) : $student->headline ?? 'View my academic achievements and certificates' }}">
    <meta property="og:image" content="{{ $student->profile_picture_url }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="profile">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $student->full_name }}">
    <meta name="twitter:description" content="{{ $student->bio ? Str::limit($student->bio, 160) : $student->headline ?? 'View my academic achievements and certificates' }}">
    <meta name="twitter:image" content="{{ $student->profile_picture_url }}">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-blue-600">Certificate Portal</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('verification.form') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        Verify Certificate
                    </a>
                    <a href="{{ route('student.login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Student Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8">
                <img src="{{ $student->profile_picture_url }}" alt="{{ $student->full_name }}" class="w-32 h-32 rounded-full border-4 border-white shadow-xl object-cover">
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-bold">{{ $student->full_name }}</h1>
                    @if($student->headline)
                        <p class="text-xl mt-2 text-blue-100">{{ $student->headline }}</p>
                    @endif
                    <div class="flex flex-wrap items-center justify-center md:justify-start mt-4 space-x-4 text-blue-100">
                        @if($student->school)
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                                {{ $student->school->name }}
                            </span>
                        @endif
                        @if($student->location)
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                {{ $student->location }}
                            </span>
                        @endif
                    </div>

                    <!-- Social Links -->
                    @if($student->website_url || $student->linkedin_url || $student->twitter_url || $student->github_url)
                        <div class="flex items-center justify-center md:justify-start mt-4 space-x-3">
                            @if($student->website_url)
                                <a href="{{ $student->website_url }}" target="_blank" class="bg-white bg-opacity-20 hover:bg-opacity-30 p-2 rounded-full transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            @endif
                            @if($student->linkedin_url)
                                <a href="{{ $student->linkedin_url }}" target="_blank" class="bg-white bg-opacity-20 hover:bg-opacity-30 p-2 rounded-full transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                </a>
                            @endif
                            @if($student->twitter_url)
                                <a href="{{ $student->twitter_url }}" target="_blank" class="bg-white bg-opacity-20 hover:bg-opacity-30 p-2 rounded-full transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                                    </svg>
                                </a>
                            @endif
                            @if($student->github_url)
                                <a href="{{ $student->github_url }}" target="_blank" class="bg-white bg-opacity-20 hover:bg-opacity-30 p-2 rounded-full transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- About Section -->
        @if($student->bio)
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">About</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $student->bio }}</p>
            </div>
        @endif

        <!-- Certificates Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Certificates & Achievements</h2>

            @if($certificates->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($certificates as $certificate)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 text-lg">
                                        {{ $certificate->template->name ?? 'Certificate' }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $certificate->school->name ?? 'N/A' }}
                                    </p>
                                </div>
                                <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>

                            @if($certificate->event)
                                <p class="text-sm text-blue-600 font-medium mb-2">
                                    {{ $certificate->event->name }}
                                </p>
                            @endif

                            @if($certificate->rank)
                                <p class="text-sm font-semibold text-green-600 mb-2">
                                    Position: {{ $certificate->rank }}
                                </p>
                            @endif

                            <p class="text-xs text-gray-500">
                                Issued on {{ $certificate->issued_at->format('M d, Y') }}
                            </p>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('verify', $certificate->certificate_id) }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center">
                                    Verify Certificate
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($certificates->hasPages())
                    <div class="mt-8">
                        {{ $certificates->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No certificates to display</h3>
                    <p class="mt-1 text-sm text-gray-500">This student hasn't made any certificates public yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
