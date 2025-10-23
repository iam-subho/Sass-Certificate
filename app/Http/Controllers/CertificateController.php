<?php

namespace App\Http\Controllers;

use App\Http\Requests\Certificate\GenerateCertificateRequest;
use App\Http\Requests\Certificate\BulkPrintCertificatesRequest;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\School;
use App\Jobs\SendCertificateEmail;
use App\Jobs\SendCertificateWhatsApp;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\LaravelPdf\Facades\Pdf;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Certificate::with(['student', 'school', 'event', 'issuer', 'approver']);

        if ($user->isSuperAdmin()) {
            // Super admin sees all certificates
        } elseif ($user->isSchoolAdmin()) {
            // School admin sees all certificates from their school
            $query->where('school_id', $user->school_id);
        } elseif ($user->isIssuer()) {
            // Issuer sees only certificates they issued
            $query->where('issuer_id', $user->id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by event
        if ($request->has('event_id') && $request->event_id != '') {
            $query->where('event_id', $request->event_id);
        }

        // Search by certificate ID or student name
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_id', 'like', "%{$search}%")
                  ->orWhereHas('student', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $certificates = $query->latest()->paginate(20);

        // Get events for filter dropdown with eager loading
        $events = $user->isSchoolAdmin() || $user->isIssuer()
            ? \App\Models\Event::where('school_id', $user->school_id)->select('id', 'name', 'school_id')->get()
            : \App\Models\Event::select('id', 'name', 'school_id')->get();

        return view('certificates.index', compact('certificates', 'events'));
    }

    /**
     * Show the form for creating a certificate.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $students = Student::with('school')->get();
        } else {
            $students = Student::where('school_id', $user->school_id)->get();
        }

        return view('certificates.create', compact('students'));
    }

    /**
     * Generate certificate for a student.
     */
    public function generate(GenerateCertificateRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $student = Student::with('school.certificateTemplate')->findOrFail($validated['student_id']);

        // School admin can only generate for their school
        if ($user->isSchoolAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $school = $student->school;

        if (!$school->certificateTemplate) {
            return back()->with('error', 'No certificate template assigned to this school.');
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('student_id', $student->id)
            ->where('school_id', $school->id)
            ->first();

        if ($existingCertificate) {
            return redirect()->route('certificates.show', $existingCertificate->id)
                ->with('info', 'Certificate already exists for this student.');
        }

        // Create certificate record (no files saved)
        $certificate = Certificate::create([
            'student_id' => $student->id,
            'school_id' => $school->id,
            'certificate_template_id' => $school->certificate_template_id,
            'issued_at' => now(),
        ]);

        return redirect()->route('certificates.show', $certificate->id)
            ->with('success', 'Certificate generated successfully.');
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        $user = auth()->user();

        // School admin can only view their school's certificates
        if ($user->isSchoolAdmin() && $certificate->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        // Issuer can only view certificates they issued
        if ($user->isIssuer() && $certificate->issuer_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $certificate->load(['student', 'school', 'template', 'event', 'issuer', 'approver']);

        $html = $this->generateCertificateHtml($certificate);

        return view('certificates.show', compact('certificate', 'html'));
    }

    /**
     * Download certificate PDF (generated on-the-fly).
     */
    public function download(Certificate $certificate)
    {
        $user = auth()->user();

        // School admin can only download their school's certificates
        if ($user->isSchoolAdmin() && $certificate->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        // Issuer can only download certificates they issued
        if ($user->isIssuer() && $certificate->issuer_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow download of approved certificates
        if ($certificate->status !== 'approved') {
            return back()->with('error', 'Only approved certificates can be downloaded.');
        }

        // Load relationships
        $certificate->load(['student', 'school', 'template', 'event']);

        // Generate certificate HTML with QR code
        $html = $this->generateCertificateHtml($certificate);

        // Generate PDF on-the-fly with Spatie (supports Tailwind!)
        return Pdf::view('certificates.pdf', ['html' => $html])
            ->format('a4')
            ->landscape()
            ->name('certificate-' . $certificate->certificate_id . '.pdf');
    }

    /**
     * Download certificate via secure token (public access).
     */
    public function downloadViaToken($token)
    {
        $certificate = Certificate::where('download_token', $token)
            ->where('status', 'approved')
            ->firstOrFail();

        // Load relationships
        $certificate->load(['student', 'school', 'template', 'event']);

        // Generate certificate HTML with QR code
        $html = $this->generateCertificateHtml($certificate);

        // Show HTML in browser with print button (faster loading)
        return view('certificates.single-print', compact('html', 'certificate'));
    }

    /**
     * Generate certificate HTML from template.
     */
    protected function generateCertificateHtml(Certificate $certificate)
    {
        $student = $certificate->student;
        $school = $certificate->school;
        $template = $certificate->template;

        // Generate QR code as base64 data URI (on-the-fly)
        $verificationUrl = $certificate->verification_url;
        $qrCodeBase64 = base64_encode(QrCode::format('png')->size(200)->generate($verificationUrl));
        $qrCodeDataUri = 'data:image/png;base64,' . $qrCodeBase64;

        $data = [
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'full_name' => $student->full_name,
            'dob' => $student->dob->format('d M Y'),
            'father_name' => $student->father_name,
            'mother_name' => $student->mother_name,
            'mobile' => $student->mobile,
            'email' => $student->email ?? '',
            'school_name' => $school->name,
            'school_email' => $school->email,
            'school_phone' => $school->phone,
            'certificate_id' => $certificate->certificate_id,
            'issued_date' => $certificate->issued_at->format('d M Y'),
            'qr_code' => $qrCodeDataUri,
            // Event and rank information
            'event_name' => $certificate->event ? $certificate->event->name : 'General Certificate',
            'event_type' => $certificate->event ? ucfirst($certificate->event->event_type) : '',
            'event_date' => $certificate->event && $certificate->event->event_date ? $certificate->event->event_date->format('d M Y') : '',
            'event_description' => $certificate->event ? ($certificate->event->description ?? '') : '',
            'rank' => $certificate->rank ?? 'Participation',
            // Convert images to base64 data URIs for PDF compatibility
            'school_logo' => $this->imageToDataUri($school->logo),
            'certificate_left_logo' => $this->imageToDataUri($school->certificate_left_logo),
            'certificate_right_logo' => $this->imageToDataUri($school->certificate_right_logo),
            'signature_left' => $this->imageToDataUri($school->signature_left),
            'signature_middle' => $this->imageToDataUri($school->signature_middle),
            'signature_right' => $this->imageToDataUri($school->signature_right),
            'signature_left_title' => $school->signature_left_title ?? '',
            'signature_middle_title' => $school->signature_middle_title ?? '',
            'signature_right_title' => $school->signature_right_title ?? '',
        ];

        return $template->render($data);
    }

    /**
     * Convert image path to base64 data URI.
     */
    protected function imageToDataUri($imagePath)
    {
        if (!$imagePath) {
            return '';
        }

        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            return '';
        }

        $imageData = file_get_contents($fullPath);
        $mimeType = mime_content_type($fullPath);
        $base64 = base64_encode($imageData);

        return 'data:' . $mimeType . ';base64,' . $base64;
    }

    /**
     * Bulk print certificates.
     */
    public function bulkPrint(BulkPrintCertificatesRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        // Get certificates with authorization check
        $certificates = Certificate::whereIn('id', $validated['certificate_ids'])
            ->with(['student', 'school', 'event', 'template'])
            ->where('status', 'approved')
            ->get();

        // Authorization check
        if ($user->isSchoolAdmin() || $user->isIssuer()) {
            $certificates = $certificates->where('school_id', $user->school_id);
        }

        if ($certificates->isEmpty()) {
            return back()->with('error', 'No certificates found or you do not have permission to view them.');
        }

        // Generate HTML for each certificate
        $certificatesHtml = [];
        foreach ($certificates as $certificate) {
            $certificatesHtml[] = $this->generateCertificateHtml($certificate);
        }

        return view('certificates.bulk-print', compact('certificatesHtml', 'certificates'));
    }

    /**
     * Send certificate via email.
     */
    public function sendEmail(Certificate $certificate)
    {
        $user = auth()->user();

        // Authorization check
        if ($user->isSchoolAdmin() && $certificate->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->isIssuer() && $certificate->issuer_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only send approved certificates
        if ($certificate->status !== 'approved') {
            return back()->with('error', 'Only approved certificates can be sent via email.');
        }

        // Check if student has email
        if (!$certificate->student->email) {
            return back()->with('error', 'Student does not have an email address.');
        }

        // Dispatch email job
        dispatch(new SendCertificateEmail($certificate));

        return back()->with('success', 'Certificate email has been queued for sending to ' . $certificate->student->email);
    }

    /**
     * Send certificate via WhatsApp.
     */
    public function sendWhatsApp(Certificate $certificate)
    {
        $user = auth()->user();

        // Authorization check
        if ($user->isSchoolAdmin() && $certificate->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->isIssuer() && $certificate->issuer_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only send approved certificates
        if ($certificate->status !== 'approved') {
            return back()->with('error', 'Only approved certificates can be sent via WhatsApp.');
        }

        // Check if student has mobile number
        if (!$certificate->student->mobile) {
            return back()->with('error', 'Student does not have a mobile number.');
        }

        // Dispatch WhatsApp job
        dispatch(new SendCertificateWhatsApp($certificate));

        return back()->with('success', 'Certificate WhatsApp message has been queued for sending to ' . $certificate->student->mobile);
    }
}
