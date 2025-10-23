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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Package name (e.g., "Basic", "Pro", "Enterprise")
            $table->text('description')->nullable(); // Package description
            $table->integer('monthly_certificate_limit'); // Number of certificates per month
            $table->integer('duration_months'); // Package duration in months
            $table->decimal('price', 10, 2); // Package price
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
