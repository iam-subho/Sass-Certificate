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
        Schema::create('student_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_option_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('numeric_value', 10, 2)->nullable();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->date('assigned_at');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Unique constraint: same student, same attribute, same date = not allowed
            $table->unique(['student_id', 'attribute_id', 'assigned_at'], 'student_attribute_date_unique');

            $table->index(['student_id', 'assigned_at']);
            $table->index(['attribute_id', 'assigned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attributes');
    }
};
