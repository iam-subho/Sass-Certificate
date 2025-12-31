<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentAttributeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    protected StudentAttributeService $attributeService;

    public function __construct(StudentAttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }
    /**
     * Display the student dashboard.
     */
    public function index()
    {
        $student = Auth::guard('student')->user();

        // Load student's certificates with relationships
        $certificates = $student->certificates()
            ->with(['template', 'event', 'school'])
            ->where('status', 'approved')
            ->latest('issued_at')
            ->paginate(10);

        $stats = [
            'total_certificates' => $student->certificates()->where('status', 'approved')->count(),
            'visible_certificates' => $student->certificates()
                ->where('status', 'approved')
                ->where('visible_on_profile', true)
                ->count(),
            'hidden_certificates' => $student->certificates()
                ->where('status', 'approved')
                ->where('visible_on_profile', false)
                ->count(),
        ];

        return view('student.dashboard', compact('student', 'certificates', 'stats'));
    }

    /**
     * Toggle certificate visibility on profile
     */
    public function toggleCertificateVisibility(Request $request, $certificateId)
    {
        $student = Auth::guard('student')->user();

        $certificate = $student->certificates()
            ->where('id', $certificateId)
            ->firstOrFail();

        $certificate->update([
            'visible_on_profile' => !$certificate->visible_on_profile,
        ]);

        $status = $certificate->visible_on_profile ? 'visible' : 'hidden';

        return back()->with('success', "Certificate is now {$status} on your public profile.");
    }

    /**
     * Display the student's attribute history with charts.
     */
    public function history(Request $request)
    {
        $student = Auth::guard('student')->user();

        // Get available attributes for filter dropdown
        $attributes = $this->attributeService->getAvailableAttributes($student->school_id);

        // Get paginated history
        $assignments = $this->attributeService->getHistory(
            $student->id,
            $request->input('attribute_id'),
            $request->input('from_date'),
            $request->input('to_date')
        );

        // Get chart data
        $chartData = $this->attributeService->getChartData(
            $student->id,
            $student->school_id,
            $request->input('attribute_id'),
            $request->input('from_date'),
            $request->input('to_date')
        );

        // Get summary statistics
        $summary = $this->attributeService->getAttributeSummary($student->id);

        return view('student.attributes.history', compact('student', 'attributes', 'assignments', 'chartData', 'summary'));
    }
}
