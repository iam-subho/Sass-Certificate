<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Certificate Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Student Portal</h1>
                <p class="text-gray-600 mt-2">Sign in to access your certificates</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                        Email or Username
                    </label>
                    <input type="text"
                           id="login"
                           name="login"
                           value="{{ old('login') }}"
                           required
                           autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('login') border-red-500 @enderror">
                    @error('login')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="{{ route('student.password.request') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Forgot password?
                    </a>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have login credentials? Contact your school administrator.
                </p>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-700">
                    Staff/Admin Login →
                </a>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('verification.form') }}" class="text-sm text-gray-600 hover:text-gray-700">
                ← Back to Certificate Verification
            </a>
        </div>
    </div>
</body>
</html>
