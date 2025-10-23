<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\School;
use App\Models\Package;
use App\Enums\InvoiceStatus;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = School::all();
        $packages = Package::all();

        if ($schools->isEmpty() || $packages->isEmpty()) {
            if ($this->command) {
                $this->command->warn('No schools or packages found. Please run SchoolSeeder and PackageSeeder first.');
            }
            return;
        }

        $invoiceCount = 0;

        foreach ($schools as $school) {
            // Get a random package for this school
            $package = $packages->random();

            // Create a paid invoice (last month)
            Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber($school, 1),
                'school_id' => $school->id,
                'month' => now()->subMonth()->format('Y-m'),
                'certificates_count' => $package->monthly_certificate_limit,
                'amount' => $package->price,
                'plan_type' => 'paid',
                'status' => InvoiceStatus::PAID->value,
                'due_date' => now()->subMonth()->addDays(30),
                'paid_date' => now()->subMonth()->addDays(15),
                'payment_method' => 'bank_transfer',
                'transaction_id' => 'TXN-' . strtoupper(substr(md5(uniqid()), 0, 10)),
                'notes' => 'Payment received on time',
                'package_meta' => $package->toArray(),
            ]);
            $invoiceCount++;

            // Create a pending invoice (current month)
            Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber($school, 2),
                'school_id' => $school->id,
                'month' => now()->format('Y-m'),
                'certificates_count' => $package->monthly_certificate_limit,
                'amount' => $package->price,
                'plan_type' => 'paid',
                'status' => InvoiceStatus::PENDING->value,
                'due_date' => now()->addDays(30),
                'paid_date' => null,
                'payment_method' => null,
                'transaction_id' => null,
                'notes' => null,
                'package_meta' => $package->toArray(),
            ]);
            $invoiceCount++;
        }

        if ($this->command) {
            $this->command->info($invoiceCount . ' invoices created successfully.');
        }
    }

    /**
     * Generate a unique invoice number for a school
     */
    private function generateInvoiceNumber(School $school, int $sequence): string
    {
        $schoolPrefix = strtoupper(substr($school->name, 0, 3));
        $timestamp = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid() . $sequence), 0, 4));

        return "INV-{$schoolPrefix}-{$timestamp}-{$random}";
    }
}
