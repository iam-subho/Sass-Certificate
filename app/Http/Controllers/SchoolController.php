<?php

namespace App\Http\Controllers;

use App\Http\Requests\School\StoreSchoolRequest;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Models\School;
use App\Models\User;
use App\Models\CertificateTemplate;
use App\Models\Package;
use App\Enums\SchoolStatus;
use App\Enums\UserRole;
use App\Services\FileUploadHandler;
use App\Services\InvoiceService;
use App\Traits\HandlesTransactions;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
    use HandlesTransactions;

    protected FileUploadHandler $fileUploadHandler;
    protected InvoiceService $invoiceService;

    public function __construct(FileUploadHandler $fileUploadHandler, InvoiceService $invoiceService)
    {
        $this->fileUploadHandler = $fileUploadHandler;
        $this->invoiceService = $invoiceService;
    }
    /**
     * Display a listing of schools.
     */
    public function index()
    {
        $schools = School::with(['certificateTemplates:id,name', 'package:id,name', 'admins:id,name,school_id'])
            ->paginate(config('pagination.schools'));
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

        return $this->executeInTransactionWithRedirect(
            function () use ($request, $validated) {
                // Handle file uploads
                $uploadedFiles = $this->fileUploadHandler->handleSchoolUploads($request);

                // Create school
                $schoolData = collect($validated)
                    ->except(['admin_name', 'admin_email', 'admin_password', 'template_ids'])
                    ->merge($uploadedFiles)
                    ->toArray();

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

                // Attach certificate templates
                if (!empty($validated['template_ids'])) {
                    $school->certificateTemplates()->attach($validated['template_ids']);
                }

                // Create school admin user
                User::create([
                    'name' => $validated['admin_name'],
                    'email' => $validated['admin_email'],
                    'password' => Hash::make($validated['admin_password']),
                    'role' => UserRole::SCHOOL_ADMIN->value,
                    'school_id' => $school->id,
                    'is_active' => true,
                ]);

                // Create initial invoice if package is assigned
                if (!empty($validated['package_id'])) {
                    $this->invoiceService->createInitialInvoice($school, $validated);
                }
            },
            'schools.index',
            'School and admin account created successfully.',
            'Failed to create school'
        );
    }

    /**
     * Display the specified school.
     */
    public function show(School $school)
    {
        $school->load([
            'certificateTemplates',
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
            'pending_certificates' => $school->certificates()->where('status', config('statuses.certificate.pending'))->count(),
            'approved_certificates' => $school->certificates()->where('status', config('statuses.certificate.approved'))->count(),
            'total_classes' => $school->classes()->count(),
            'total_events' => $school->events()->count(),
        ];

        // Get invoice statistics using InvoiceService
        $invoiceStats = $this->invoiceService->getSchoolInvoiceSummary($school);

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

        return $this->executeInTransactionWithRedirect(
            function () use ($request, $validated, $school) {
                // Handle file uploads
                $uploadedFiles = $this->fileUploadHandler->handleSchoolUploads($request, $school);

                // Track if package changed for invoice generation
                $packageChanged = $request->has('package_id') && $request->package_id != $school->package_id;

                // Handle package change - update plan details
                if ($packageChanged) {
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
                        $validated['monthly_certificate_limit'] = config('certificates.default_monthly_limit');
                        $validated['certificates_issued_this_month'] = $school->certificates_issued_this_month;
                    }
                }

                // Handle status update for super admin
                if (auth()->user()->isSuperAdmin() && $request->has('status')) {
                    // If changing to approved, set approved_at and approved_by
                    if ($request->status == SchoolStatus::APPROVED->value && $school->status != SchoolStatus::APPROVED->value) {
                        $validated['approved_at'] = now();
                        $validated['approved_by'] = auth()->id();
                    }
                } else {
                    // Remove status from validated data if not super admin
                    unset($validated['status']);
                }

                // Extract template_ids before updating
                $templateIds = $validated['template_ids'] ?? [];
                $updateData = collect($validated)
                    ->except(['template_ids'])
                    ->merge($uploadedFiles)
                    ->toArray();

                $school->update($updateData);

                // Sync certificate templates
                if (!empty($templateIds)) {
                    $school->certificateTemplates()->sync($templateIds);
                }

                // Create invoice only if package changed
                if ($packageChanged && !empty($request->package_id)) {
                    $package = Package::find($request->package_id)->toArray();
                    $this->invoiceService->createInitialInvoice($school, $validated);
                }
            },
            'schools.index',
            'School updated successfully.',
            'Failed to update school'
        );
    }

    /**
     * Remove the specified school.
     */
    public function destroy(School $school)
    {
        // Delete associated files
        $this->fileUploadHandler->deleteSchoolFiles($school);

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
        $schools = School::where('status', SchoolStatus::PENDING->value)
            ->with(['admins:id,name,email,school_id', 'certificateTemplates:id,name'])
            ->latest()
            ->paginate(config('pagination.schools'));

        return view('schools.pending', compact('schools'));
    }

    /**
     * Approve a school (Super Admin only).
     */
    public function approve(School $school)
    {
        $school->update([
            'status' => SchoolStatus::APPROVED->value,
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
            'status' => SchoolStatus::REJECTED->value,
        ]);

        return back()->with('success', 'School rejected.');
    }

    /**
     * Suspend a school (Super Admin only).
     */
    public function suspend(School $school)
    {
        $school->update([
            'status' => SchoolStatus::SUSPENDED->value,
        ]);

        return back()->with('success', 'School suspended.');
    }

}
