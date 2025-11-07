<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Authentication fields
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('password')->nullable()->after('username');
            $table->rememberToken()->after('password');
            $table->timestamp('email_verified_at')->nullable()->after('remember_token');

            // Profile fields (LinkedIn-style)
            $table->text('bio')->nullable()->after('email_verified_at');
            $table->string('profile_picture')->nullable()->after('bio');
            $table->string('headline')->nullable()->after('profile_picture'); // Like "Student at XYZ School"
            $table->string('location')->nullable()->after('headline');
            $table->string('website_url')->nullable()->after('location');
            $table->string('linkedin_url')->nullable()->after('website_url');
            $table->string('twitter_url')->nullable()->after('linkedin_url');
            $table->string('github_url')->nullable()->after('twitter_url');

            // Privacy & Settings
            $table->boolean('profile_public')->default(true)->after('github_url');
            $table->boolean('is_active')->default(false)->after('profile_public'); // Account activation status

            // Timestamps for last login
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'password',
                'remember_token',
                'email_verified_at',
                'bio',
                'profile_picture',
                'headline',
                'location',
                'website_url',
                'linkedin_url',
                'twitter_url',
                'github_url',
                'profile_public',
                'is_active',
                'last_login_at',
            ]);
        });
    }
};
