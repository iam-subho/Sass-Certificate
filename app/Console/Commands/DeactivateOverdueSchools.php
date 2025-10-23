<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeactivateOverdueSchools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schools:deactivate-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate schools with invoices overdue by more than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for schools with overdue invoices...');

        // Find invoices that are overdue by more than 7 days
        $overdueInvoices = Invoice::with('school')
            ->where('status', 'pending')
            ->whereDate('due_date', '<', now()->subDays(7))
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->info('No schools with significantly overdue invoices found.');
            return 0;
        }

        $this->info("Found {$overdueInvoices->count()} overdue invoice(s).");

        $deactivatedCount = 0;
        $alreadyInactiveCount = 0;

        foreach ($overdueInvoices as $invoice) {
            $school = $invoice->school;

            // Skip if school is already inactive
            if (!$school->is_active) {
                $alreadyInactiveCount++;
                $this->warn("School '{$school->name}' is already inactive.");
                continue;
            }

            try {
                // Deactivate the school
                $school->update([
                    'is_active' => false,
                    'status' => 'suspended',
                ]);

                $deactivatedCount++;
                $daysOverdue = now()->diffInDays($invoice->due_date);

                $this->info("✓ Deactivated '{$school->name}' - Invoice {$invoice->invoice_number} overdue by {$daysOverdue} days");

                // Log the deactivation for audit trail
                Log::warning("School deactivated due to overdue invoice", [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'days_overdue' => $daysOverdue,
                    'amount' => $invoice->amount,
                    'due_date' => $invoice->due_date->toDateString(),
                ]);

            } catch (\Exception $e) {
                $this->error("✗ Failed to deactivate '{$school->name}': " . $e->getMessage());

                Log::error("Failed to deactivate school", [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Schools deactivated: {$deactivatedCount}");
        if ($alreadyInactiveCount > 0) {
            $this->info("Already inactive: {$alreadyInactiveCount}");
        }
        $this->info('Deactivation check completed.');

        return 0;
    }
}
