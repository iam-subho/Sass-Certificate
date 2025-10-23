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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('logo')->nullable(); // School logo
            $table->foreignId('certificate_template_id')->nullable()->constrained('certificate_templates')->onDelete('set null');

            // Certificate branding
            $table->string('certificate_left_logo')->nullable();
            $table->string('certificate_right_logo')->nullable();

            // Signatures
            $table->string('signature_left')->nullable();
            $table->string('signature_middle')->nullable();
            $table->string('signature_right')->nullable();

            // Signature titles
            $table->string('signature_left_title')->nullable();
            $table->string('signature_middle_title')->nullable();
            $table->string('signature_right_title')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
