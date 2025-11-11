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
        Schema::create('inter_school_event_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inter_school_event_id')->constrained('inter_school_events')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->enum('status', ['joined', 'completed'])->default('joined');
            $table->boolean('approved_by_school')->default(true);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['inter_school_event_id', 'student_id'], 'ise_student_unique');
            $table->index(['inter_school_event_id', 'school_id'], 'ise_student_event_school_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inter_school_event_student');
    }
};
