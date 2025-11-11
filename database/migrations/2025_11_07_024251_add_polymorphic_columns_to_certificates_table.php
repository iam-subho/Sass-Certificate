<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Make event_id nullable for backward compatibility
            $table->foreignId('event_id')->nullable()->change();

            // Add polymorphic columns
            $table->string('certifiable_type')->nullable()->after('event_id');
            $table->unsignedBigInteger('certifiable_id')->nullable()->after('certifiable_type');

            // Add index for polymorphic relationship
            $table->index(['certifiable_type', 'certifiable_id']);
        });

        // Backfill existing certificates with polymorphic data
        DB::table('certificates')
            ->whereNotNull('event_id')
            ->update([
                'certifiable_type' => 'App\\Models\\Event',
                'certifiable_id' => DB::raw('event_id')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex(['certifiable_type', 'certifiable_id']);
            $table->dropColumn(['certifiable_type', 'certifiable_id']);
        });
    }
};
