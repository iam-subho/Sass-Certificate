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
        Schema::table('schools', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'suspended', 'rejected'])->default('pending')->after('certificate_template_id');
            $table->string('plan_type')->default('free')->after('status'); // free, basic, premium, enterprise
            $table->date('plan_start_date')->nullable()->after('plan_type');
            $table->date('plan_expiry_date')->nullable()->after('plan_start_date');
            $table->integer('monthly_certificate_limit')->default(100)->after('plan_expiry_date');
            $table->integer('certificates_issued_this_month')->default(0)->after('monthly_certificate_limit');
            $table->timestamp('approved_at')->nullable()->after('certificates_issued_this_month');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status', 'plan_type', 'plan_start_date', 'plan_expiry_date',
                'monthly_certificate_limit', 'certificates_issued_this_month',
                'approved_at', 'approved_by'
            ]);
        });
    }
};
