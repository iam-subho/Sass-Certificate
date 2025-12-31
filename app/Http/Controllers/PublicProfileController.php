<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentAttributeService;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    protected StudentAttributeService $attributeService;

    public function __construct(StudentAttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    /**
     * Display the public profile of a student.
     */
    public function show($username)
    {
        $student = Student::where('username', $username)
            ->with(['school', 'class'])
            ->firstOrFail();

        // Check if profile is public
        if (!$student->profile_public) {
            abort(403, 'This profile is private.');
        }

        // Get visible certificates
        $certificates = $student->visibleCertificates()
            ->with(['template', 'event', 'school'])
            ->paginate(12);

        // Get attribute chart data and summary for public profile
        $chartData = $this->attributeService->getChartData($student->id, $student->school_id);
        $attributeSummary = $this->attributeService->getAttributeSummary($student->id);

        return view('student.profile.public', compact('student', 'certificates', 'chartData', 'attributeSummary'));
    }
}
