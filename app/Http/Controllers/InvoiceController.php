<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\School;
use App\Enums\InvoiceStatus;
use App\Enums\SchoolStatus;
use App\Services\InvoiceService;
use App\Traits\AuthorizesSchoolResources;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use AuthorizesSchoolResources;

    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Build the base query with user scope
        $query = Invoice::query()->with(['school:id,name']);
        $query = $this->scopeByUserRole($query, 'school_id', $user);

        // Apply filters
        $this->applyFilters($query, $request);

        // Get invoices with pagination
        $invoices = $query->latest()->paginate(config('pagination.invoices'))->withQueryString();

        // Get schools for filter dropdown (Super Admin only)
        $schools = $user->isSuperAdmin()
            ? School::select('id', 'name')->orderBy('name')->get()
            : collect();

        return view('invoices.index', compact('invoices', 'schools'));
    }

    /**
     * Apply filters to invoice query
     */
    protected function applyFilters($query, Request $request): void
    {
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $this->authorizeSchoolResource($invoice);

        $invoice->load(['school']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice (mark as paid).
     */
    public function edit(Invoice $invoice)
    {
        $this->authorizeSuperAdmin('Only super admins can edit invoices.');

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice (mark as paid, update notes).
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $validated = $request->validated();

        // If marking as paid, handle payment logic
        if (isset($validated['status']) &&
            $validated['status'] == InvoiceStatus::PAID->value &&
            $invoice->status !== InvoiceStatus::PAID->value) {

            // Mark invoice as paid
            $this->invoiceService->markAsPaid($invoice, [
                'payment_method' => $validated['payment_method'] ?? null,
                'transaction_id' => $validated['transaction_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update school's package and plan
            $this->invoiceService->updateSchoolPlanOnPayment($invoice);
        } else {
            // Just update the invoice
            $invoice->update($validated);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Show overdue invoices.
     */
    public function overdue()
    {
        $this->authorizeSuperAdmin('Only super admins can view overdue invoices.');

        $invoices = Invoice::with(['school:id,name'])
            ->where('status', InvoiceStatus::PENDING->value)
            ->where('due_date', '<', now())
            ->latest()
            ->paginate(config('pagination.invoices'));

        return view('invoices.overdue', compact('invoices'));
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        $this->authorizeSchoolResource($invoice);

        $invoice->load(['school']);

        return view('invoices.pdf', compact('invoice'));
    }
}
