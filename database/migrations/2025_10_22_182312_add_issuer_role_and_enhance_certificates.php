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
        // Add issuer role to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('school_admin')->change();
            // Roles: super_admin, school_admin, issuer
        });

        // Enhance certificates table (event_id will be added in a separate migration after events table exists)
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('issuer_id')->nullable()->after('school_id')->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('is_valid');
            $table->string('rank')->nullable()->after('status'); // For rank holders: "1st", "2nd", "3rd", "Participation"
            $table->boolean('sent_via_email')->default(false)->after('rank');
            $table->boolean('sent_via_whatsapp')->default(false)->after('sent_via_email');
            $table->timestamp('approved_at')->nullable()->after('issued_at');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        });

        // Add section to students table (class_id will be added in a separate migration after classes table exists)
        Schema::table('students', function (Blueprint $table) {
            $table->string('section')->nullable()->after('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('section');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['issuer_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'issuer_id', 'status', 'rank',
                'sent_via_email', 'sent_via_whatsapp', 'approved_at', 'approved_by'
            ]);
        });
    }
};
