<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Certificate Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen py-8">
    <div class="max-w-2xl w-full mx-auto px-4">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Student Registration</h1>
                <p class="text-gray-600 mt-2">Create your student account</p>
            </div>

            <form method="POST" action="{{ route('student.register') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name *
                        </label>
                        <input type="text"
                               id="first_name"
                               name="first_name"
                               value="{{ old('first_name') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name *
                        </label>
                        <input type="text"
                               id="last_name"
                               name="last_name"
                               value="{{ old('last_name') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address *
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                        Mobile Number *
                    </label>
                    <input type="text"
                           id="mobile"
                           name="mobile"
                           value="{{ old('mobile') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mobile') border-red-500 @enderror">
                    @error('mobile')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Birth *
                    </label>
                    <input type="date"
                           id="dob"
                           name="dob"
                           value="{{ old('dob') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dob') border-red-500 @enderror">
                    @error('dob')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="father_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Father's Name (Optional)
                        </label>
                        <input type="text"
                               id="father_name"
                               name="father_name"
                               value="{{ old('father_name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Mother's Name (Optional)
                        </label>
                        <input type="text"
                               id="mother_name"
                               name="mother_name"
                               value="{{ old('mother_name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password * (Minimum 8 characters)
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

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password *
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Note:</strong> Your account will be pending approval from your school administrator. You'll be able to login once approved.
                    </p>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105">
                    Register
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('student.login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                        Login here
                    </a>
                </p>
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
