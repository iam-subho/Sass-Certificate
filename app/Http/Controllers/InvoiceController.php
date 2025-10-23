<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\School;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Build the base query
        if ($user->isSuperAdmin()) {
            $query = Invoice::with(['school:id,name']);
        } elseif ($user->isSchoolAdmin()) {
            $query = Invoice::where('school_id', $user->school_id);
        } else {
            abort(403, 'Unauthorized action.');
        }

        // Apply filters
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

        // Get invoices with pagination
        $invoices = $query->latest()->paginate(20)->withQueryString();

        // Get schools for filter dropdown (Super Admin only)
        $schools = $user->isSuperAdmin()
            ? School::select('id', 'name')->orderBy('name')->get()
            : collect();

        return view('invoices.index', compact('invoices', 'schools'));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $user = auth()->user();

        // School admin can only view their own school's invoices
        if ($user->isSchoolAdmin() && $invoice->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->load(['school']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice (mark as paid).
     */
    public function edit(Invoice $invoice)
    {
        $user = auth()->user();

        // School admin can only edit their own school's invoices
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice (mark as paid, update notes).
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $validated = $request->validated();

        // If marking as paid, set paid_date and update school package
        if (isset($validated['status']) && $validated['status'] === 'paid' && $invoice->status !== 'paid') {
            $validated['paid_date'] = $validated['paid_date'] ?? now();

            // Update school's package and plan for next month
            $this->updateSchoolPlanOnPayment($invoice);
        }

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Update school's plan when invoice is paid.
     */
    protected function updateSchoolPlanOnPayment(Invoice $invoice)
    {
        $school = $invoice->school;

        // Extend plan by 1 month from current expiry or from now
        $startDate = $school->plan_expiry_date && $school->plan_expiry_date->isFuture()
            ? $school->plan_expiry_date
            : now();

        $school->update([
            'plan_start_date' => $startDate,
            'plan_expiry_date' => $startDate->copy()->addMonth(),
            'plan_type' => $invoice->plan_type,
            'monthly_certificate_limit' => $invoice->certificates_count,
            'certificates_issued_this_month' => 0, // Reset monthly counter
            'is_active' => true, // Reactivate if deactivated
            'status' => 'approved', // Ensure status is approved
        ]);
    }

    /**
     * Show overdue invoices.
     */
    public function overdue()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $invoices = Invoice::with(['school:id,name'])
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->latest()
            ->paginate(20);

        return view('invoices.overdue', compact('invoices'));
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        $user = auth()->user();

        // School admin can only download their own school's invoices
        if ($user->isSchoolAdmin() && $invoice->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->load(['school']);

        return view('invoices.pdf', compact('invoice'));
    }
}
