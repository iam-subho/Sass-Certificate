<?php

namespace App\Services;

use App\Enums\SchoolStatus;
use App\Models\Invoice;
use App\Models\School;
use App\Models\Package;
use App\Enums\InvoiceStatus;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Create an invoice for a school
     *
     * @param School $school
     * @param Package|null $package
     * @param array $additionalData
     * @return Invoice
     */
    public function createInvoice(School $school, ?Package $package = null, array $additionalData = []): Invoice
    {
        $invoiceData = [
            'school_id' => $school->id,
            'invoice_number' => $this->generateInvoiceNumber($school),
            'status' => InvoiceStatus::PENDING->value,
            'due_date' => now()->addDays(30),
            'month'    => date('Y-m'),
        ];

        if ($package) {
            $invoiceData = array_merge($invoiceData, [
                'plan_type' => 'paid',
                'package_id' => $package->id,
                'amount' => $package->price,
                'certificates_count' =>$package->monthly_certificate_limit,
                'package_meta' => $package->toArray(),
                'description' => "Subscription for {$package->name} package",
            ]);
        }

        // Merge any additional data
        $invoiceData = array_merge($invoiceData, $additionalData);

        $invoice =  Invoice::create($invoiceData);

        $school->makePending();

        return $invoice;
    }

    /**
     * Generate a unique invoice number for a school
     *
     * @param School $school
     * @return string
     */
    public function generateInvoiceNumber(School $school): string
    {
        $schoolPrefix = strtoupper(substr($school->name, 0, 3));
        $timestamp = now()->format('Ymd');
        $random = Str::upper(Str::random(4));

        // Keep generating until we get a unique one
        do {
            $invoiceNumber = "INV-{$schoolPrefix}-{$timestamp}-{$random}";
            $exists = Invoice::where('invoice_number', $invoiceNumber)->exists();

            if ($exists) {
                $random = Str::upper(Str::random(4));
            }
        } while ($exists);

        return $invoiceNumber;
    }

    /**
     * Mark an invoice as paid
     *
     * @param Invoice $invoice
     * @param array $paymentData
     * @return Invoice
     */
    public function markAsPaid(Invoice $invoice, array $paymentData = []): Invoice
    {
        $invoice->update([
            'status' => InvoiceStatus::PAID->value,
            'paid_date' => now(),
            'payment_method' => $paymentData['payment_method'] ?? null,
            'transaction_id' => $paymentData['transaction_id'] ?? null,
            'notes' => $paymentData['notes'] ?? null,
        ]);

        return $invoice->fresh();
    }

    /**
     * Update school plan when invoice is paid
     *
     * @param Invoice $invoice
     * @return void
     */
    public function updateSchoolPlanOnPayment(Invoice $invoice): void
    {
        $school = $invoice->school;
        $package = collect($invoice->package_meta);

        // Calculate plan dates
        $planStartDate = now();

        $planExpiryDate = $planStartDate->copy()->addMonths($package['duration_months']);

        // Update school plan
        $school->update([
            'plan_type' => 'paid',
            'plan_start_date' => $planStartDate,
            'plan_expiry_date' => $planExpiryDate,
            'monthly_certificate_limit' => $invoice->certificates_count,
            'package_id' => $package['id'],
        ]);

        // If school was pending and now has a paid plan, auto-approve
        if ($school->status == SchoolStatus::PENDING->value) {
            $school->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);
        }

    }

    /**
     * Create initial invoice when school is created or package is changed
     *
     * @param School $school
     * @param array $data
     * @return Invoice|null
     */
    public function createInitialInvoice(School $school, array $data): ?Invoice
    {
        $packageId = $data['package_id'] ?? null;

        if (!$packageId) {
            return null;
        }

        $package = Package::find($packageId);

        if (!$package) {
            return null;
        }

        return $this->createInvoice($school, $package, [
            'description' => "Initial subscription for {$package->name} package",
        ]);
    }

    /**
     * Check if invoice is overdue and update status
     *
     * @param Invoice $invoice
     * @return bool
     */
    public function checkAndMarkOverdue(Invoice $invoice): bool
    {
        if ($invoice->status === InvoiceStatus::PENDING->value && $invoice->due_date < now()) {
            $invoice->update(['status' => InvoiceStatus::OVERDUE->value]);
            return true;
        }

        return false;
    }

    /**
     * Get invoice summary for a school
     *
     * @param School $school
     * @return array
     */
    public function getSchoolInvoiceSummary(School $school): array
    {
        return [
            'total_invoices' => $school->invoices()->count(),
            'pending_invoices' => $school->invoices()->where('status', InvoiceStatus::PENDING->value)->count(),
            'paid_invoices' => $school->invoices()->where('status', InvoiceStatus::PAID->value)->count(),
            'overdue_invoices' => $school->invoices()
                ->where('status', InvoiceStatus::PENDING->value)
                ->where('due_date', '<', now())
                ->count(),
            'total_paid_amount' => $school->invoices()->where('status', InvoiceStatus::PAID->value)->sum('amount'),
            'pending_amount' => $school->invoices()->where('status', InvoiceStatus::PENDING->value)->sum('amount'),
        ];
    }
}
