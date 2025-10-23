<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateMonthlyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly invoices for schools whose plan is expiring today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly invoice generation...');

        // Find schools whose plan expires today
        $schools = School::with('package')
            ->whereDate('plan_expiry_date', now()->toDateString())
            ->where('is_active', true)
            ->where('status', 'approved')
            ->get();

        if ($schools->isEmpty()) {
            $this->info('No schools with expiring plans found.');
            return 0;
        }

        $this->info("Found {$schools->count()} school(s) with expiring plans.");

        $successCount = 0;
        $errorCount = 0;

        foreach ($schools as $school) {
            try {
                // Check if invoice already exists for next month
                $nextMonth = now()->addMonth()->format('Y-m');
                $existingInvoice = Invoice::where('school_id', $school->id)
                    ->where('month', $nextMonth)
                    ->first();

                if ($existingInvoice) {
                    $this->warn("Invoice already exists for {$school->name} for {$nextMonth}");
                    continue;
                }

                // Generate invoice number
                $currentMonth = now()->format('Ym');
                $invoiceNumber = 'INV-' . $currentMonth . '-' . str_pad($school->id, 4, '0', STR_PAD_LEFT);

                // Get amount and certificate count from package
                $amount = 0;
                $certificatesCount = 10;
                $planType = 'Free';

                if ($school->package) {
                    $amount = $school->package->price;
                    $certificatesCount = $school->package->monthly_certificate_limit;
                    $planType = $school->package->name;
                } else {
                    // Use current school settings if no package
                    $certificatesCount = $school->monthly_certificate_limit ?? 10;
                }

                // If amount is 0, mark as paid automatically
                $status = $amount == 0 ? 'paid' : 'pending';
                $paidDate = $amount == 0 ? now() : null;
                $paymentMethod = $amount == 0 ? 'Free Plan' : null;
                $notes = $amount == 0
                    ? 'Free plan - Auto-generated and marked as paid'
                    : 'Auto-generated invoice for plan renewal';

                // Create invoice
                Invoice::create([
                    'school_id' => $school->id,
                    'invoice_number' => $invoiceNumber,
                    'month' => $nextMonth,
                    'amount' => $amount,
                    'certificates_count' => $certificatesCount,
                    'plan_type' => $planType,
                    'status' => $status,
                    'due_date' => now()->addDays(7), // Due in 7 days
                    'paid_date' => $paidDate,
                    'payment_method' => $paymentMethod,
                    'notes' => $notes,
                ]);

                $successCount++;
                $this->info("✓ Generated invoice for {$school->name} - {$invoiceNumber}");

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("✗ Failed to generate invoice for {$school->name}: " . $e->getMessage());
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Successfully generated: {$successCount} invoice(s)");
        if ($errorCount > 0) {
            $this->error("Failed: {$errorCount} invoice(s)");
        }
        $this->info('Invoice generation completed.');

        return 0;
    }
}
