<?php

namespace App\Http\Controllers;

use App\Models\{Certificate, School, Event, Student};
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * School-level analytics dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Basic stats
        $stats = [
            'total_certificates' => Certificate::where('school_id', $user->school_id)->count(),
            'this_month' => Certificate::where('school_id', $user->school_id)
                ->whereMonth('issued_at', now()->month)
                ->whereYear('issued_at', now()->year)
                ->count(),
            'pending_approvals' => Certificate::where('school_id', $user->school_id)
                ->where('status', 'pending')
                ->count(),
            'total_students' => Student::where('school_id', $user->school_id)->count(),
            'total_events' => Event::where('school_id', $user->school_id)->count(),
            'active_events' => Event::where('school_id', $user->school_id)->count(),
            'monthly_limit' => $user->school->monthly_certificate_limit,
            'certificates_issued_this_month' => $user->school->certificates_issued_this_month,
        ];

        // Certificates by event
        $certificatesByEvent = Certificate::where('school_id', $user->school_id)
            ->select('event_id', DB::raw('count(*) as count'))
            ->with('event:id,name')
            ->groupBy('event_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Certificates by month (last 6 months)
        $certificatesByMonth = Certificate::where('school_id', $user->school_id)
            ->select(
                DB::raw('DATE_FORMAT(issued_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('issued_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Certificates by class
        $certificatesByClass = Certificate::where('certificates.school_id', $user->school_id)
            ->join('students', 'certificates.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->select('classes.name', DB::raw('count(*) as count'))
            ->groupBy('classes.name')
            ->orderBy('count', 'desc')
            ->get();

        // Top students (most certificates)
        $topStudents = Certificate::where('school_id', $user->school_id)
            ->select('student_id', DB::raw('count(*) as count'))
            ->with('student:id,first_name,last_name')
            ->groupBy('student_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('analytics.index', compact('stats', 'certificatesByEvent', 'certificatesByMonth', 'certificatesByClass', 'topStudents'));
    }

    /**
     * Super admin analytics dashboard.
     */
    public function superAdmin()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        // Basic stats
        $stats = [
            'total_schools' => School::count(),
            'approved_schools' => School::where('status', 'approved')->count(),
            'pending_schools' => School::where('status', 'pending')->count(),
            'suspended_schools' => School::where('status', 'suspended')->count(),
            'total_certificates' => Certificate::count(),
            'this_month_certificates' => Certificate::whereMonth('issued_at', now()->month)
                ->whereYear('issued_at', now()->year)
                ->count(),
            'total_students' => Student::count(),
            'total_events' => Event::count(),
        ];

        // Top schools by certificate count
        $topSchools = School::withCount(['certificates' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->orderBy('certificates_count', 'desc')
            ->take(10)
            ->get();

        // Certificates by month (last 12 months)
        $byMonth = Certificate::select(
                DB::raw('DATE_FORMAT(issued_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('issued_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Schools by plan type
        $byPlan = School::select('plan_type', DB::raw('count(*) as count'))
            ->groupBy('plan_type')
            ->get();

        // Revenue overview (certificates * plan pricing - simplified)
        $totalRevenue = School::sum('certificates_issued_this_month') * 10; // Assume $10 per certificate

        return view('analytics.super', compact('stats', 'topSchools', 'byMonth', 'byPlan', 'totalRevenue'));
    }
}
