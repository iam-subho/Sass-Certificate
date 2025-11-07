<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-green-500 to-teal-600 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden p-8">
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Verify Certificate</h1>
                <p class="text-gray-600">Enter certificate ID to verify authenticity</p>
            </div>

            <form method="POST" action="{{ route('verification.search') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="certificate_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Certificate ID
                    </label>
                    <input id="certificate_id" type="text" name="certificate_id" required autofocus
                        placeholder="e.g., CERT-XXXXXXXXXX"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Verify Certificate
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('student.login') }}" class="text-sm text-gray-600 hover:text-gray-800 underline">
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
