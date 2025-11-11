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
        Schema::create('inter_school_event_school', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inter_school_event_id')->constrained('inter_school_events')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->enum('status', ['pending', 'joined', 'rejected'])->default('pending');
            $table->boolean('can_students_join')->default(true);
            $table->json('allowed_classes')->nullable(); // Array of class IDs
            $table->boolean('manual_approval_required')->default(false);
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->unique(['inter_school_event_id', 'school_id'], 'ise_school_unique');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inter_school_event_school');
    }
};
