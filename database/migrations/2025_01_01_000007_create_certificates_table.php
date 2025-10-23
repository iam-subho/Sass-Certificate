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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_id')->unique(); // Unique certificate identifier
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('certificate_template_id')->constrained('certificate_templates')->onDelete('cascade');
            $table->text('qr_code')->nullable(); // QR code image path or data
            $table->string('pdf_path')->nullable(); // Path to generated PDF
            $table->timestamp('issued_at');
            $table->boolean('is_valid')->default(true);
            $table->timestamps();

            $table->index('certificate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
