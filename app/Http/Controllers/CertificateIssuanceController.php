<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Event;
use App\Jobs\SendCertificateEmail;
use App\Jobs\SendCertificateWhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificateIssuanceController extends Controller
{
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

        return view('issue.step3', compact('event', 'students'));
    }

    /**
     * Step 4: Email/WhatsApp Options
     */
    public function step4(Request $request)
    {
        $sessionData = session('certificate_issuance', []);

        // Handle POST request (coming from step3)
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'certificate_type' => 'required|in:participation,rank',
                'ranks' => 'nullable|array',
                'ranks.*' => 'nullable|string',
                'student_ids' => 'required|array',
            ]);

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

        // Check if school is approved
        if (!$school->isApproved()) {
            return back()->with('error', 'Your school is not approved yet.');
        }

        // Check if plan is expired
        if ($school->isPlanExpired()) {
            return back()->with('error', 'Your subscription plan has expired.');
        }

        // Check certificate limit
        $certificateCount = count($sessionData['selected_student_ids']);
        if ($school->certificates_issued_this_month + $certificateCount > $school->monthly_certificate_limit) {
            return back()->with('error', 'Certificate limit exceeded. Please upgrade your plan.');
        }

        $event = Event::find($sessionData['event_id']);
        $students = Student::whereIn('id', $sessionData['selected_student_ids'])->get();

        DB::beginTransaction();
        try {
            $certificates = [];
            $skippedStudents = [];
            $preventDuplicates = config('certificates.prevent_duplicate_per_event', true);

            foreach ($students as $student) {
                // Check for duplicate certificate if prevention is enabled
                if ($preventDuplicates) {
                    $existingCertificate = Certificate::where('student_id', $student->id)
                        ->where('event_id', $event->id)
                        ->first();

                    if ($existingCertificate) {
                        $skippedStudents[] = $student->full_name;
                        continue; // Skip this student
                    }
                }

                // Determine rank
                $rank = null;
                if ($sessionData['certificate_type'] === 'rank') {
                    $rank = $sessionData['ranks'][$student->id] ?? 'Participation';
                } else {
                    $rank = 'Participation';
                }

                // Create certificate
                $certificate = Certificate::create([
                    'student_id' => $student->id,
                    'school_id' => $school->id,
                    'certificate_template_id' => $event->certificate_template_id ?? $school->certificate_template_id,
                    'event_id' => $event->id,
                    'issuer_id' => $user->id,
                    'status' => $user->isIssuer() ? 'pending' : 'approved', // Issuers create pending, admins create approved
                    'rank' => $rank,
                    'issued_at' => now(),
                    'approved_at' => $user->isIssuer() ? null : now(),
                    'approved_by' => $user->isIssuer() ? null : $user->id,
                ]);

                $certificates[] = $certificate;
            }

            // If no certificates were created
            if (empty($certificates)) {
                DB::rollBack();
                $message = 'No certificates were generated. All selected students already have certificates for this event.';
                return back()->with('error', $message);
            }

            // Update school's monthly count with actual certificates created
            $actualCertificateCount = count($certificates);
            $school->increment('certificates_issued_this_month', $actualCertificateCount);

            DB::commit();

            // Queue email/WhatsApp sending if requested
            if ($validated['send_email'] ?? false) {
                foreach ($certificates as $certificate) {
                    // Dispatch email job
                    dispatch(new SendCertificateEmail($certificate));
                }
            }

            if ($validated['send_whatsapp'] ?? false) {
                foreach ($certificates as $certificate) {
                    // Dispatch WhatsApp job
                    dispatch(new SendCertificateWhatsApp($certificate));
                }
            }

            // Clear session
            session()->forget('certificate_issuance');

            // Build success message
            $message = $user->isIssuer()
                ? "{$actualCertificateCount} certificate(s) created successfully and sent for approval."
                : "{$actualCertificateCount} certificate(s) generated successfully.";

            // Add warning about skipped students if any
            if (!empty($skippedStudents)) {
                $skippedNames = implode(', ', $skippedStudents);
                $message .= " Note: Duplicate certificates were skipped for: {$skippedNames}";
            }

            return redirect()->route('certificates.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to generate certificates: ' . $e->getMessage());
        }
    }
}
