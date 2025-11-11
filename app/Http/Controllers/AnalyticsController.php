<?php

namespace App\Http\Controllers;

use App\Models\{Certificate, School, Event, Student, InterSchoolEvent, Invoice};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * School-level analytics dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()){
            $monthlyLimit = 99999999999999999999;
            $issuedthisMonth = 0;
        }else{
            $monthlyLimit = $user->school->monthly_certificate_limit;
            $issuedthisMonth = $user->school->monthly_certificate_issued_this_month;
        }

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
            'monthly_limit' => $monthlyLimit,
            'certificates_issued_this_month' => $issuedthisMonth,
        ];

        // Certificates by event
        $certificatesByEvent = Certificate::where('school_id', $user->school_id)
            ->join('events', 'certificates.certifiable_id', '=', 'events.id')
            ->where('certificates.certifiable_type', 'App\\Models\\Event')
            ->select('events.name as event_name', DB::raw('count(*) as count'))
            ->groupBy('events.name')
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
            ->select('classes.name as class_name', DB::raw('count(*) as count'))
            ->groupBy('classes.name')
            ->orderBy('count', 'desc')
            ->get();

        // Top students (most certificates)
        $topStudents = Certificate::where('certificates.school_id', $user->school_id)
            ->join('students', 'certificates.student_id', '=', 'students.id')
            ->select(
                DB::raw("CONCAT(students.first_name, ' ', students.last_name) as student_name"),
                DB::raw('count(*) as count')
            )
            ->groupBy('students.id', 'students.first_name', 'students.last_name')
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
            'active_schools' => School::where('status', 'approved')->count(),
            'approved_schools' => School::where('status', 'approved')->count(),
            'pending_schools' => School::where('status', 'pending')->count(),
            'suspended_schools' => School::where('status', 'suspended')->count(),
            'total_certificates' => Certificate::count(),
            'this_month_certificates' => Certificate::whereMonth('issued_at', now()->month)
                ->whereYear('issued_at', now()->year)
                ->count(),
            'total_students' => Student::count(),
            'total_events' => Event::count(),
            'total_revenue' => 0, // Placeholder
        ];

        // Top schools by certificate count
        $topSchools = School::select('schools.school_name', DB::raw('count(certificates.id) as count'))
            ->leftJoin('certificates', 'schools.id', '=', 'certificates.school_id')
            ->where(function($query) {
                $query->where('certificates.status', 'approved')
                      ->orWhereNull('certificates.id');
            })
            ->groupBy('schools.id', 'schools.school_name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Certificates by month (last 12 months)
        $certificatesByMonth = Certificate::select(
                DB::raw('DATE_FORMAT(issued_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('issued_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Schools by plan type
        $schoolsByPlan = School::select('plan_type', DB::raw('count(*) as count'))
            ->groupBy('plan_type')
            ->get();

        // Recent invoices
        $recentInvoices = Invoice::with('school')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Revenue overview (certificates * plan pricing - simplified)
        $totalRevenue = School::sum('certificates_issued_this_month') * 10; // Assume $10 per certificate
        $stats['total_revenue'] = $totalRevenue;

        return view('analytics.super', compact('stats', 'topSchools', 'certificatesByMonth', 'schoolsByPlan', 'recentInvoices', 'totalRevenue'));
    }

    /**
     * Get calendar events for super admin (only inter-school events).
     */
    public function getCalendarEventsSuperAdmin()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $interSchoolEvents = InterSchoolEvent::all();

        // Color palette for events
        $colors = [
            ['bg' => '#EF4444', 'border' => '#DC2626'], // red
            ['bg' => '#F59E0B', 'border' => '#D97706'], // amber
            ['bg' => '#10B981', 'border' => '#059669'], // emerald
            ['bg' => '#3B82F6', 'border' => '#2563EB'], // blue
            ['bg' => '#8B5CF6', 'border' => '#7C3AED'], // violet
            ['bg' => '#EC4899', 'border' => '#DB2777'], // pink
            ['bg' => '#14B8A6', 'border' => '#0D9488'], // teal
            ['bg' => '#F97316', 'border' => '#EA580C'], // orange
            ['bg' => '#6366F1', 'border' => '#4F46E5'], // indigo
            ['bg' => '#A855F7', 'border' => '#9333EA'], // purple
            ['bg' => '#06B6D4', 'border' => '#0891B2'], // cyan
            ['bg' => '#84CC16', 'border' => '#65A30D'], // lime
        ];

        $events = [];
        $colorIndex = 0;
        foreach ($interSchoolEvents as $event) {
            $color = $colors[$colorIndex % count($colors)];
            $events[] = [
                'id' => 'inter-' . $event->id,
                'title' => $event->title,
                'start' => $event->start_date->format('Y-m-d'),
                'end' => $event->end_date->addDay()->format('Y-m-d'), // Add 1 day to make end date inclusive
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'extendedProps' => [
                    'type' => 'inter-school',
                    'venue' => $event->venue,
                    'status' => $event->status,
                    'category' => $event->event_category,
                ]
            ];
            $colorIndex++;
        }

        return response()->json($events);
    }

    /**
     * Get calendar events for school admin/issuer (school events + joined inter-school events).
     */
    public function getCalendarEventsSchool()
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Color palette for events
        $colors = [
            ['bg' => '#EF4444', 'border' => '#DC2626'], // red
            ['bg' => '#F59E0B', 'border' => '#D97706'], // amber
            ['bg' => '#10B981', 'border' => '#059669'], // emerald
            ['bg' => '#3B82F6', 'border' => '#2563EB'], // blue
            ['bg' => '#8B5CF6', 'border' => '#7C3AED'], // violet
            ['bg' => '#EC4899', 'border' => '#DB2777'], // pink
            ['bg' => '#14B8A6', 'border' => '#0D9488'], // teal
            ['bg' => '#F97316', 'border' => '#EA580C'], // orange
            ['bg' => '#6366F1', 'border' => '#4F46E5'], // indigo
            ['bg' => '#A855F7', 'border' => '#9333EA'], // purple
            ['bg' => '#06B6D4', 'border' => '#0891B2'], // cyan
            ['bg' => '#84CC16', 'border' => '#65A30D'], // lime
        ];

        $events = [];
        $colorIndex = 0;

        // Get school events
        $schoolEvents = Event::where('school_id', $schoolId)->get();
        foreach ($schoolEvents as $event) {
            $color = $colors[$colorIndex % count($colors)];
            $events[] = [
                'id' => 'school-' . $event->id,
                'title' => $event->name,
                'start' => $event->event_date->format('Y-m-d'),
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'extendedProps' => [
                    'type' => 'school',
                    'description' => $event->description,
                    'event_type' => $event->event_type,
                ]
            ];
            $colorIndex++;
        }

        // Get joined inter-school events
        $interSchoolEvents = InterSchoolEvent::whereHas('schools', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId)
                  ->where('inter_school_event_school.status', 'joined');
        })->get();

        foreach ($interSchoolEvents as $event) {
            $color = $colors[$colorIndex % count($colors)];
            $events[] = [
                'id' => 'inter-' . $event->id,
                'title' => $event->title . ' (Inter-School)',
                'start' => $event->start_date->format('Y-m-d'),
                'end' => $event->end_date->addDay()->format('Y-m-d'), // Add 1 day to make end date inclusive
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'extendedProps' => [
                    'type' => 'inter-school',
                    'venue' => $event->venue,
                    'status' => $event->status,
                    'category' => $event->event_category,
                ]
            ];
            $colorIndex++;
        }

        return response()->json($events);
    }
}
