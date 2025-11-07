@extends('student.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Your Profile</h2>

        <!-- Profile Information Form -->
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Profile Picture -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                <div class="flex items-center space-x-6">
                    <img src="{{ $student->profile_picture_url }}" alt="{{ $student->full_name }}" class="w-24 h-24 rounded-full object-cover">
                    <div class="flex-1">
                        <input type="file" name="profile_picture" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG or GIF (MAX. 2MB)</p>
                        @error('profile_picture')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @if($student->profile_picture)
                        <form method="POST" action="{{ route('student.profile.delete-picture') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Remove
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username * <span class="text-gray-500 text-xs">(Only lowercase letters, numbers, and hyphens)</span>
                </label>
                <input type="text" id="username" name="username" value="{{ old('username', $student->username) }}" required pattern="[a-z0-9\-]+" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('username') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Your public profile will be: {{ url('/profile/') }}/<span class="font-semibold">{{ $student->username }}</span></p>
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile *</label>
                    <input type="text" id="mobile" name="mobile" value="{{ old('mobile', $student->mobile) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mobile') border-red-500 @enderror">
                    @error('mobile')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="headline" class="block text-sm font-medium text-gray-700 mb-2">
                    Headline <span class="text-gray-500 text-xs">(e.g., "Student at XYZ School")</span>
                </label>
                <input type="text" id="headline" name="headline" value="{{ old('headline', $student->headline) }}" maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                <textarea id="bio" name="bio" rows="4" maxlength="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tell us about yourself...">{{ old('bio', $student->bio) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters</p>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" id="location" name="location" value="{{ old('location', $student->location) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="City, Country">
            </div>

            <!-- Social Links -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Links</h3>
                <div class="space-y-4">
                    <div>
                        <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url" id="website_url" name="website_url" value="{{ old('website_url', $student->website_url) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="https://example.com">
                        @error('website_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn</label>
                        <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $student->linkedin_url) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="https://linkedin.com/in/username">
                        @error('linkedin_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">Twitter</label>
                        <input type="url" id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $student->twitter_url) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="https://twitter.com/username">
                        @error('twitter_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="github_url" class="block text-sm font-medium text-gray-700 mb-2">GitHub</label>
                        <input type="url" id="github_url" name="github_url" value="{{ old('github_url', $student->github_url) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="https://github.com/username">
                        @error('github_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Privacy Settings</h3>
                <div class="flex items-center">
                    <input type="checkbox" id="profile_public" name="profile_public" value="1" {{ old('profile_public', $student->profile_public) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <label for="profile_public" class="ml-2 text-sm text-gray-700">
                        Make my profile public (Anyone can view your profile and visible certificates)
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-gray-700 font-medium">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Change Password</h3>

        <form method="POST" action="{{ route('student.profile.update-password') }}" class="space-y-4">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" id="current_password" name="current_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_password') border-red-500 @enderror">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
