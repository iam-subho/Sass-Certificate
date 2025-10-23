<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Event;
use App\Enums\CertificateStatus;
use App\Jobs\SendCertificateEmail;
use App\Jobs\SendCertificateWhatsApp;
use App\Traits\HandlesTransactions;
use Illuminate\Http\Request;

class CertificateIssuanceController extends Controller
{
    use HandlesTransactions;
    /**
     * Step 1: Select Class/Section → Load Students
     */
    public function step1()
    {
        $user = auth()->user();

        $classes = SchoolClass::where('school_id', $user->school_id)
            ->active()
            ->get();

        return view('issue.step1', compact('classes'));
    }

    /**
     * Load students for selected class/section (AJAX)
     */
    public function loadStudents(Request $request)
    {
        $user = auth()->user();

        $query = Student::where('school_id', $user->school_id)
            ->where('class_id', $request->class_id);

        if ($request->section) {
            $query->where('section', $request->section);
        }

        $students = $query->get();

        return response()->json([
            'students' => $students
        ]);
    }

    /**
     * Step 2: Pick Event/Competition
     */
    public function step2(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section' => 'nullable|string',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $user = auth()->user();

        $events = Event::where('school_id', $user->school_id)
            ->active()
            ->get();

        // Store data in session for next steps
        session([
            'certificate_issuance' => [
                'class_id' => $validated['class_id'],
                'section' => $validated['section'] ?? null,
                'student_ids' => $validated['student_ids'],
            ]
        ]);

        $students = Student::whereIn('id', $validated['student_ids'])->get();
        $class = SchoolClass::find($validated['class_id']);

        return view('issue.step2', compact('events', 'students', 'class'));
    }

    /**
     * Step 3: Select Certificate Type (Participation or Rank)
     */
    public function step3(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $sessionData = session('certificate_issuance', []);
        $sessionData['event_id'] = $validated['event_id'];
        session(['certificate_issuance' => $sessionData]);

        $event = Event::find($validated['event_id']);
        $students = Student::whereIn('id', $sessionData['student_ids'])->get();

        // Get available templates for the school
        $user = auth()->user();
        $availableTemplates = $user->school->certificateTemplates;

        return view('issue.step3', compact('event', 'students', 'availableTemplates'));
    }

    /**
     * Step 4: Email/WhatsApp Options
     */
    public function step4(Request $request)
    {
        $sessionData = session('certificate_issuance', []);

        // Handle POST request (coming from step3)
        if ($request->isMethod('post')) {
            $certificateTypes = array_values(config('certificates.types'));

            $validated = $request->validate([
                'certificate_template_id' => 'required|exists:certificate_templates,id',
                'certificate_type' => 'required|in:' . implode(',', $certificateTypes),
                'ranks' => 'nullable|array',
                'ranks.*' => 'nullable|string',
                'student_ids' => 'required|array',
            ]);

            $sessionData['certificate_template_id'] = $validated['certificate_template_id'];
            $sessionData['certificate_type'] = $validated['certificate_type'];
            $sessionData['ranks'] = $validated['ranks'] ?? [];
            $sessionData['selected_student_ids'] = $validated['student_ids'];
            session(['certificate_issuance' => $sessionData]);
        }

        // Handle GET request (refresh or back button)
        if (!isset($sessionData['event_id']) || !isset($sessionData['selected_student_ids'])) {
            return redirect()->route('issue.step1')->with('error', 'Session expired. Please start again.');
        }

        $event = Event::find($sessionData['event_id']);
        $students = Student::whereIn('id', $sessionData['selected_student_ids'])->get();

        return view('issue.step4', compact('event', 'students'));
    }

    /**
     * Confirm and Generate Certificates
     */
    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'send_email' => 'boolean',
            'send_whatsapp' => 'boolean',
        ]);

        $sessionData = session('certificate_issuance', []);
        if (!$sessionData) {
            return redirect()->route('issue.step1')->with('error', 'Session expired. Please start again.');
        }

        $user = auth()->user();
        $school = $user->school;

        // Validate school status and limits
        $validationError = $this->validateSchoolForIssuance($school, count($sessionData['selected_student_ids']));
        if ($validationError) {
            return back()->with('error', $validationError);
        }

        $event = Event::find($sessionData['event_id']);
        $students = Student::whereIn('id', $sessionData['selected_student_ids'])->get();

        return $this->executeInTransaction(function () use ($request, $validated, $sessionData, $user, $school, $event, $students) {
            $certificates = [];
            $skippedStudents = [];
            $preventDuplicates = config('certificates.prevent_duplicate_per_event');

            foreach ($students as $student) {
                // Check for duplicate certificate if prevention is enabled
                if ($preventDuplicates && $this->isDuplicateCertificate($student->id, $event->id)) {
                    $skippedStudents[] = $student->full_name;
                    continue;
                }

                // Create certificate
                $certificate = $this->createCertificate($student, $school, $event, $user, $sessionData);
                $certificates[] = $certificate;
            }

            // If no certificates were created
            if (empty($certificates)) {
                return back()->with('error', 'No certificates were generated. All selected students already have certificates for this event.');
            }

            // Update school's monthly count
            $school->increment('certificates_issued_this_month', count($certificates));

            // Dispatch notifications
            $this->dispatchNotifications($certificates, $validated);

            // Clear session
            session()->forget('certificate_issuance');

            // Build success message
            $message = $this->buildSuccessMessage($user, count($certificates), $skippedStudents);

            return redirect()->route('certificates.index')->with('success', $message);
        }) ?? back()->with('error', 'Failed to generate certificates.');
    }

    /**
     * Validate school status and certificate limits
     */
    protected function validateSchoolForIssuance($school, int $certificateCount): ?string
    {
        if (!$school->isApproved()) {
            return 'Your school is not approved yet.';
        }

        if ($school->isPlanExpired()) {
            return 'Your subscription plan has expired.';
        }

        if ($school->certificates_issued_this_month + $certificateCount > $school->monthly_certificate_limit) {
            return 'Certificate limit exceeded. Please upgrade your plan.';
        }

        return null;
    }

    /**
     * Check if certificate already exists for student and event
     */
    protected function isDuplicateCertificate(int $studentId, int $eventId): bool
    {
        return Certificate::where('student_id', $studentId)
            ->where('event_id', $eventId)
            ->exists();
    }

    /**
     * Create a certificate
     */
    protected function createCertificate($student, $school, $event, $user, array $sessionData): Certificate
    {
        $rank = $this->determineRank($sessionData, $student->id);

        return Certificate::create([
            'student_id' => $student->id,
            'school_id' => $school->id,
            'certificate_template_id' => $sessionData['certificate_template_id'],
            'event_id' => $event->id,
            'issuer_id' => $user->id,
            'status' => $user->isIssuer() ? CertificateStatus::PENDING->value : CertificateStatus::APPROVED->value,
            'rank' => $rank,
            'issued_at' => now(),
            'approved_at' => $user->isIssuer() ? null : now(),
            'approved_by' => $user->isIssuer() ? null : $user->id,
        ]);
    }

    /**
     * Determine rank for certificate
     */
    protected function determineRank(array $sessionData, int $studentId): string
    {
        if ($sessionData['certificate_type'] === config('certificates.types.rank')) {
            return $sessionData['ranks'][$studentId] ?? 'Participation';
        }

        return 'Participation';
    }

    /**
     * Dispatch email and WhatsApp notifications
     */
    protected function dispatchNotifications(array $certificates, array $options): void
    {
        foreach ($certificates as $certificate) {
            if ($options['send_email'] ?? false) {
                dispatch(new SendCertificateEmail($certificate));
            }

            if ($options['send_whatsapp'] ?? false) {
                dispatch(new SendCertificateWhatsApp($certificate));
            }
        }
    }

    /**
     * Build success message
     */
    protected function buildSuccessMessage($user, int $count, array $skippedStudents): string
    {
        $message = $user->isIssuer()
            ? "{$count} certificate(s) created successfully and sent for approval."
            : "{$count} certificate(s) generated successfully.";

        if (!empty($skippedStudents)) {
            $skippedNames = implode(', ', $skippedStudents);
            $message .= " Note: Duplicate certificates were skipped for: {$skippedNames}";
        }

        return $message;
    }
}
