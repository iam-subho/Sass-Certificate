<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Mail\CertificateIssuedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SendCertificateEmail implements ShouldQueue
{
    use Queueable;

    protected $certificate;

    /**
     * Create a new job instance.
     */
    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load necessary relationships
        $this->certificate->load(['student', 'school', 'event', 'template']);

        // Send email to student
        if ($this->certificate->student->email) {
            // Generate PDF and save temporarily
            $pdfPath = $this->generateCertificatePdf();

            try {
                // Send email with PDF attachment
                Mail::to($this->certificate->student->email)
                    ->send(new CertificateIssuedMail($this->certificate, $pdfPath));

                // Update certificate to mark email as sent
                $this->certificate->update(['sent_via_email' => true]);
            } finally {
                // Delete PDF file after sending (regardless of success/failure)
                if ($pdfPath && Storage::exists($pdfPath)) {
                    Storage::delete($pdfPath);
                }
            }
        }
    }

    /**
     * Generate certificate PDF and return storage path.
     */
    protected function generateCertificatePdf(): string
    {
        // Generate certificate HTML with QR code
        $html = $this->generateCertificateHtml();

        // Ensure temp directory exists
        $tempDir = 'temp/certificates';
        if (!Storage::exists($tempDir)) {
            Storage::makeDirectory($tempDir);
        }

        // Create unique filename
        $filename = $tempDir . '/certificate-' . $this->certificate->certificate_id . '-' . time() . '.pdf';

        // Get full path for saving
        $fullPath = Storage::path($filename);

        // Generate PDF and save to storage
        Pdf::view('certificates.pdf', ['html' => $html])
            ->format('a4')
            ->landscape()
            ->save($fullPath);

        return $filename;
    }

    /**
     * Generate certificate HTML from template.
     */
    protected function generateCertificateHtml(): string
    {
        $certificate = $this->certificate;
        $student = $certificate->student;
        $school = $certificate->school;
        $template = $certificate->template;

        // Generate QR code as base64 data URI
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
            'event_name' => $certificate->event ? $certificate->event->name : 'General Certificate',
            'event_type' => $certificate->event ? ucfirst($certificate->event->event_type) : '',
            'event_date' => $certificate->event && $certificate->event->event_date ? $certificate->event->event_date->format('d M Y') : '',
            'event_description' => $certificate->event ? ($certificate->event->description ?? '') : '',
            'rank' => $certificate->rank ?? 'Participation',
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
    protected function imageToDataUri($imagePath): string
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
}
