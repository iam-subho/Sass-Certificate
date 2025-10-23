<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification Result</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($found)
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-green-500 px-6 py-4">
                <div class="flex items-center justify-center">
                    <svg class="h-8 w-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 class="text-2xl font-bold text-white">Certificate Verified</h1>
                </div>
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Certificate Details</h2>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Certificate ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $certificate->certificate_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Issued Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificate->issued_at->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($certificate->is_valid)
                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">Valid</span>
                                @else
                                <span class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">Invalid</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="border-t border-gray-200 pt-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Student Information</h2>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->dob->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Father's Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->father_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Mother's Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->mother_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Mobile</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->mobile }}</dd>
                        </div>
                        @if($student->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->email }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">School Information</h2>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">School Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $school->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $school->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $school->phone }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-red-500 px-6 py-4">
                <div class="flex items-center justify-center">
                    <svg class="h-8 w-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 class="text-2xl font-bold text-white">Certificate Not Found</h1>
                </div>
            </div>

            <div class="p-8 text-center">
                <p class="text-gray-600 mb-6">
                    The certificate with ID <strong class="font-mono">{{ $certificate_id }}</strong> was not found in our database.
                </p>
                <p class="text-sm text-gray-500 mb-6">
                    Please verify the certificate ID and try again.
                </p>
                <a href="{{ route('verification.form') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Verify Another Certificate
                </a>
            </div>
        </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('verification.form') }}" class="text-sm text-gray-600 hover:text-gray-800 underline">
                Verify Another Certificate
            </a>
        </div>
    </div>
</body>
</html>
