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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('month'); // 2025-01, 2025-02
            $table->integer('certificates_count')->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('plan_type'); // Plan at time of invoice
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
