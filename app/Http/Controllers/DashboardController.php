<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\School;
use App\Models\Student;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Event;
use App\Models\SchoolClass;

class DashboardController extends Controller
{
    /**
     * Show the dashboard based on user role.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } elseif ($user->isIssuer()) {
            return $this->issuerDashboard();
        }

        return $this->schoolAdminDashboard();
    }

    /**
     * Super Admin Dashboard.
     */
    protected function superAdminDashboard()
    {
        $stats = [
            'total_schools' => School::count(),
            'active_schools' => School::where('status', 'approved')->count(),
            'pending_schools' => School::where('status', 'pending')->count(),
            'total_students' => Student::count(),
            'total_certificates' => Certificate::count(),
            'total_templates' => CertificateTemplate::count(),
            'certificates_this_month' => Certificate::whereMonth('issued_at', now()->month)->count(),
            'invoices_this_month' => Invoice::whereMonth('created_at', now()->month)->count(),
            'invoices_pending_this_month' => Invoice::pending()->whereMonth('created_at', now()->month)->count(),
        ];

        $recentSchools = School::latest()->take(5)->get();
        $recentCertificates = Certificate::with(['student', 'school', 'event'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.super-admin', compact('stats', 'recentSchools', 'recentCertificates'));
    }

    /**
     * School Admin Dashboard.
     */
    protected function schoolAdminDashboard()
    {
        $user = auth()->user();
        $school = $user->school;

        if (!$school) {
            abort(403, 'No school assigned to your account.');
        }

        // Load package relationship
        $school->load('package');

        $stats = [
            'total_students' => $school->students()->count(),
            'total_certificates' => $school->certificates()->count(),
            'certificates_this_month' => $school->certificates()
                ->whereMonth('issued_at', now()->month)
                ->count(),
            'pending_approvals' => $school->certificates()->where('status', 'pending')->count(),
            'active_events' => Event::where('school_id', $school->id)->where('is_active', true)->count(),
            'total_classes' => SchoolClass::where('school_id', $school->id)->count(),
        ];

        $recentStudents = $school->students()->latest()->take(5)->get();
        $recentCertificates = $school->certificates()
            ->with(['student', 'event', 'issuer'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.school-admin', compact('school', 'stats', 'recentStudents', 'recentCertificates'));
    }

    /**
     * Issuer (Teacher/Staff) Dashboard.
     */
    protected function issuerDashboard()
    {
        $user = auth()->user();
        $school = $user->school;

        if (!$school) {
            abort(403, 'No school assigned to your account.');
        }

        $stats = [
            'my_certificates' => Certificate::where('issuer_id', $user->id)->count(),
            'pending_approval' => Certificate::where('issuer_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Certificate::where('issuer_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => Certificate::where('issuer_id', $user->id)->where('status', 'rejected')->count(),
            'this_month' => Certificate::where('issuer_id', $user->id)
                ->whereMonth('issued_at', now()->month)
                ->count(),
        ];

        $recentCertificates = Certificate::where('issuer_id', $user->id)
            ->with(['student', 'event'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.issuer', compact('school', 'stats', 'recentCertificates'));
    }
}
