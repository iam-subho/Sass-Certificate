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
        // Create pivot table for many-to-many relationship
        Schema::create('certificate_template_school', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('certificate_template_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Ensure unique combinations
            $table->unique(['school_id', 'certificate_template_id'], 'ct_school_unique');
        });

        // Migrate existing data from schools.certificate_template_id to pivot table
        DB::statement('
            INSERT INTO certificate_template_school (school_id, certificate_template_id, created_at, updated_at)
            SELECT id, certificate_template_id, NOW(), NOW()
            FROM schools
            WHERE certificate_template_id IS NOT NULL
        ');

        // Remove the certificate_template_id column from schools table
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['certificate_template_id']);
            $table->dropColumn('certificate_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back certificate_template_id column to schools table
        Schema::table('schools', function (Blueprint $table) {
            $table->foreignId('certificate_template_id')->nullable()->constrained('certificate_templates')->onDelete('set null');
        });

        // Migrate data back from pivot table to schools.certificate_template_id
        // (Only the first template will be kept for each school)
        DB::statement('
            UPDATE schools s
            JOIN (
                SELECT school_id, MIN(certificate_template_id) as certificate_template_id
                FROM certificate_template_school
                GROUP BY school_id
            ) ct ON s.id = ct.school_id
            SET s.certificate_template_id = ct.certificate_template_id
        ');

        // Drop the pivot table
        Schema::dropIfExists('certificate_template_school');
    }
};
