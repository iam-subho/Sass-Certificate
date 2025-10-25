<?php

namespace App\Jobs;

use App\Models\Certificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SendCertificateWhatsApp implements ShouldQueue
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

        // Check if student has mobile number
        if (!$this->certificate->student->mobile) {
            Log::warning("No mobile number for student: {$this->certificate->student->full_name}");
            return;
        }

        $pdfPath = null;

        try {
            // Generate PDF and save temporarily
            $pdfPath = $this->generateCertificatePdf();

            // Prepare WhatsApp caption message
            $caption = $this->prepareCaption();

            // Send via Meta WhatsApp Business API
            $this->sendViaMetaAPI($pdfPath, $caption);

            // Update certificate to mark WhatsApp as sent
            $this->certificate->update(['sent_via_whatsapp' => true]);

            Log::info("WhatsApp message with PDF sent successfully", [
                'certificate_id' => $this->certificate->certificate_id,
                'mobile' => $this->certificate->student->mobile,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message", [
                'certificate_id' => $this->certificate->certificate_id,
                'mobile' => $this->certificate->student->mobile,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } finally {
            // Delete PDF file after sending (regardless of success/failure)
            if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
            }
        }
    }

    /**
     * Send message with document via Meta WhatsApp Business API.
     */
    protected function sendViaMetaAPI(string $pdfPath, string $caption): void
    {
        $phoneNumberId = config('services.meta_whatsapp.phone_number_id');
        $accessToken = config('services.meta_whatsapp.access_token');

        if (!$phoneNumberId || !$accessToken) {
            Log::warning("Meta WhatsApp credentials not configured. Message logged only.");
            Log::info("WhatsApp message would be sent to: {$this->certificate->student->mobile}", [
                'pdf_path' => $pdfPath,
                'caption' => $caption,
            ]);
            return;
        }

        // Format phone number (remove any non-numeric characters)
        $to = preg_replace('/[^0-9]/', '', $this->certificate->student->mobile);

        // Get public URL for the PDF
        $pdfUrl = Storage::disk('public')->url($pdfPath);

        // Make sure it's a full URL
        if (!filter_var($pdfUrl, FILTER_VALIDATE_URL)) {
            $pdfUrl = url($pdfUrl);
        }

        // Meta WhatsApp API endpoint
        $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

        // Prepare payload for document message
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'document',
            'document' => [
                'link' => $pdfUrl,
                'caption' => $caption,
                'filename' => 'certificate-' . $this->certificate->certificate_id . '.pdf',
            ],
        ];

        // Send request using Laravel HTTP client
        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->post($url, $payload);

        if (!$response->successful()) {
            throw new \Exception("Meta API error: " . $response->body());
        }
    }

    /**
     * Prepare the WhatsApp caption message.
     */
    protected function prepareCaption(): string
    {
        $student = $this->certificate->student;
        $school = $this->certificate->school;
        $event = $this->certificate->event;

        $message = "🎓 *Congratulations {$student->full_name}!*\n\n";
        $message .= "Your certificate has been issued by *{$school->name}*.\n\n";
        $message .= "📄 *Certificate Details:*\n";
        $message .= "• Certificate ID: {$this->certificate->certificate_id}\n";

        if ($event) {
            $message .= "• Event: {$event->name}\n";
        }

        if ($this->certificate->rank) {
            $message .= "• Achievement: {$this->certificate->rank}\n";
        }

        $message .= "• Issued Date: {$this->certificate->issued_at->format('F d, Y')}\n\n";
        $message .= "🔍 *Verify your certificate:*\n";
        $message .= "{$this->certificate->verification_url}";

        return $message;
    }

    /**
     * Generate certificate PDF and return public storage path.
     */
    protected function generateCertificatePdf(): string
    {
        // Generate certificate HTML with QR code
        $html = $this->generateCertificateHtml();

        // Ensure temp directory exists
        $tempDir = 'temp/certificates';
        if (!Storage::disk('public')->exists($tempDir)) {
            Storage::disk('public')->makeDirectory($tempDir);
        }

        // Create unique filename
        $filename = $tempDir . '/certificate-' . $this->certificate->certificate_id . '-' . time() . '.pdf';

        // Get full path for saving
        $fullPath = Storage::disk('public')->path($filename);

        // Generate PDF and save to public storage
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
