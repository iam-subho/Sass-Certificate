<?php

namespace App\Http\Controllers;

use App\Http\Requests\School\StoreSchoolRequest;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Models\School;
use App\Models\User;
use App\Models\CertificateTemplate;
use App\Models\Package;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Str;

class SchoolController extends Controller
{
    /**
     * Display a listing of schools.
     */
    public function index()
    {
        $schools = School::with(['certificateTemplate:id,name', 'package:id,name', 'admins:id,name,school_id'])
            ->paginate(15);
        return view('schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        $templates = CertificateTemplate::where('is_active', true)->select('id', 'name', 'description')->get();
        $packages = Package::where('is_active', true)->select('id', 'name', 'description', 'price', 'monthly_certificate_limit')->get();
        return view('schools.create', compact('templates', 'packages'));
    }

    /**
     * Store a newly created school.
     */
    public function store(StoreSchoolRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Handle file uploads
            $fileFields = ['logo', 'certificate_left_logo', 'certificate_right_logo',
                           'signature_left', 'signature_middle', 'signature_right'];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $validated[$field] = $request->file($field)->store('schools', 'public');
                }
            }

            // Create school
            $schoolData = collect($validated)->except(['admin_name', 'admin_email', 'admin_password'])->toArray();

            // If package is assigned, set plan details
            if (!empty($validated['package_id'])) {
                $package = Package::find($validated['package_id']);
                if ($package) {
                    $schoolData['plan_type'] = 'paid';
                    $schoolData['plan_start_date'] = now();
                    $schoolData['plan_expiry_date'] = now()->addMonths($package->duration_months);
                    $schoolData['monthly_certificate_limit'] = $package->monthly_certificate_limit;
                    $schoolData['certificates_issued_this_month'] = 0;
                }
            }

            $school = School::create($schoolData);

            // Create school admin user
            User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => 'school_admin',
                'school_id' => $school->id,
                'is_active' => true,
            ]);

            // Always create initial invoice based on package
            $this->createInitialInvoice($school, $validated);

            DB::commit();

            return redirect()->route('schools.index')
                ->with('success', 'School and admin account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create school: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified school.
     */
    public function show(School $school)
    {
        $school->load([
            'certificateTemplate',
            'admins',
            'issuers',
            'classes',
            'events',
            'approver'
        ]);

        // Get statistics
        $stats = [
            'total_students' => $school->students()->count(),
            'total_certificates' => $school->certificates()->count(),
            'pending_certificates' => $school->certificates()->where('status', 'pending')->count(),
            'approved_certificates' => $school->certificates()->where('status', 'approved')->count(),
            'total_classes' => $school->classes()->count(),
            'total_events' => $school->events()->count(),
        ];

        // Get invoice statistics
        $invoiceStats = [
            'total_invoices' => $school->invoices()->count(),
            'pending_invoices' => $school->invoices()->where('status', 'pending')->count(),
            'paid_invoices' => $school->invoices()->where('status', 'paid')->count(),
            'overdue_invoices' => $school->invoices()
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->count(),
            'total_paid_amount' => $school->invoices()->where('status', 'paid')->sum('amount'),
            'pending_amount' => $school->invoices()->where('status', 'pending')->sum('amount'),
        ];

        // Get recent invoices
        $recentInvoices = $school->invoices()->latest()->limit(5)->get();

        return view('schools.show', compact('school', 'stats', 'invoiceStats', 'recentInvoices'));
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school)
    {
        $templates = CertificateTemplate::where('is_active', true)->select('id', 'name', 'description')->get();
        $packages = Package::where('is_active', true)->select('id', 'name', 'description', 'price', 'monthly_certificate_limit')->get();
        return view('schools.edit', compact('school', 'templates', 'packages'));
    }

    /**
     * Update the specified school.
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Handle file uploads
            $fileFields = ['logo', 'certificate_left_logo', 'certificate_right_logo',
                           'signature_left', 'signature_middle', 'signature_right'];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    // Delete old file
                    if ($school->$field) {
                        Storage::disk('public')->delete($school->$field);
                    }
                    $validated[$field] = $request->file($field)->store('schools', 'public');
                }
            }

            // Track if package changed for invoice generation
            $packageChanged = false;

            // Handle package change - update plan details
            if ($request->has('package_id') && $request->package_id != $school->package_id) {
                $packageChanged = true;

                if (!empty($request->package_id)) {
                    $package = Package::find($request->package_id);
                    if ($package) {
                        $validated['plan_type'] = 'package';
                        $validated['plan_start_date'] = now();
                        $validated['plan_expiry_date'] = now()->addMonths($package->duration_months);
                        $validated['monthly_certificate_limit'] = $package->monthly_certificate_limit;
                        $validated['certificates_issued_this_month'] = $school->certificates_issued_this_month;
                    }
                } else {
                    $validated['plan_type'] = 'free';
                    $validated['plan_start_date'] = null;
                    $validated['plan_expiry_date'] = null;
                    $validated['monthly_certificate_limit'] = 10;
                    $validated['certificates_issued_this_month'] = $school->certificates_issued_this_month;
                }
            }

            // Handle status update for super admin
            if (auth()->user()->isSuperAdmin() && $request->has('status')) {
                // If changing to approved, set approved_at and approved_by
                if ($request->status == 'approved' && $school->status != 'approved') {
                    $validated['approved_at'] = now();
                    $validated['approved_by'] = auth()->id();
                }
            } else {
                // Remove status from validated data if not super admin
                unset($validated['status']);
            }

            $school->update($validated);

            // Create invoice only if package changed
            if ($packageChanged) {
                $this->createInitialInvoice($school, $validated);
            }

            DB::commit();

            return redirect()->route('schools.index')
                ->with('success', 'School updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update school: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified school.
     */
    public function destroy(School $school)
    {
        // Delete associated files
        $fileFields = ['logo', 'certificate_left_logo', 'certificate_right_logo',
                       'signature_left', 'signature_middle', 'signature_right'];

        foreach ($fileFields as $field) {
            if ($school->$field) {
                Storage::disk('public')->delete($school->$field);
            }
        }

        $school->delete();

        return redirect()->route('schools.index')
            ->with('success', 'School deleted successfully.');
    }

    /**
     * Toggle school active status.
     */
    public function toggleStatus(School $school)
    {
        $school->update(['is_active' => !$school->is_active]);

        return redirect()->route('schools.index')
            ->with('success', 'School status updated successfully.');
    }

    /**
     * Display pending schools for approval (Super Admin only).
     */
    public function pending()
    {
        $schools = School::where('status', 'pending')
            ->with(['admins:id,name,email,school_id', 'certificateTemplate:id,name'])
            ->latest()
            ->paginate(20);

        return view('schools.pending', compact('schools'));
    }

    /**
     * Approve a school (Super Admin only).
     */
    public function approve(School $school)
    {
        $school->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'School approved successfully.');
    }

    /**
     * Reject a school (Super Admin only).
     */
    public function reject(School $school)
    {
        $school->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'School rejected.');
    }

    /**
     * Suspend a school (Super Admin only).
     */
    public function suspend(School $school)
    {
        $school->update([
            'status' => 'suspended',
        ]);

        return back()->with('success', 'School suspended.');
    }

    /**
     * Create initial invoice for a school.
     */
    protected function createInitialInvoice(School $school, array $data)
    {
        // Reload school to get package relationship
        $school->load('package');

        // Get amount and certificate count from package or use defaults
        if ($school->package) {
            $amount = $school->package->price ?? 0;
            $certificatesCount = $school->package->monthly_certificate_limit ?? 10;
            $planType = $school->package->name ?? 'Free';
        } else {
            // No package - use defaults
            $amount = 0;
            $certificatesCount = 10;
            $planType = 'Free';
        }

        // Generate invoice number
        $currentMonth = now()->format('Ym');
        $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad($school->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));

        // Set due date
        $dueDate = now()->addDays(7);

        // If amount is 0, mark as paid automatically
        $status = $amount == 0 ? 'paid' : 'pending';
        $paidDate = $amount == 0 ? now() : null;
        $paymentMethod = $amount == 0 ? 'Free Plan' : null;

        // Create invoice
        Invoice::create([
            'school_id' => $school->id,
            'invoice_number' => $invoiceNumber,
            'month' => now()->format('Y-m'),
            'amount' => $amount,
            'certificates_count' => $certificatesCount,
            'plan_type' => $planType,
            'status' => $status,
            'due_date' => $dueDate,
            'paid_date' => $paidDate,
            'payment_method' => $paymentMethod,
            'notes' => $amount == 0 ? 'Free plan - No payment required' : null,
        ]);

        $school->makePending();
    }
}
