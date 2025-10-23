<?php

namespace App\Jobs;

use App\Models\Certificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

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
        $this->certificate->load(['student', 'school', 'event']);

        // Check if student has mobile number
        if (!$this->certificate->student->mobile) {
            Log::warning("No mobile number for student: {$this->certificate->student->full_name}");
            return;
        }

        // Prepare WhatsApp message
        $message = $this->prepareMessage();

        try {
            // Send via Meta WhatsApp Business API
            $this->sendViaMetaAPI($message);

            // Update certificate to mark WhatsApp as sent
            $this->certificate->update(['sent_via_whatsapp' => true]);

            Log::info("WhatsApp message sent successfully", [
                'certificate_id' => $this->certificate->certificate_id,
                'mobile' => $this->certificate->student->mobile,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message", [
                'certificate_id' => $this->certificate->certificate_id,
                'mobile' => $this->certificate->student->mobile,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send message via Meta WhatsApp Business API.
     */
    protected function sendViaMetaAPI(string $message): void
    {
        $phoneNumberId = config('services.meta_whatsapp.phone_number_id');
        $accessToken = config('services.meta_whatsapp.access_token');

        if (!$phoneNumberId || !$accessToken) {
            Log::warning("Meta WhatsApp credentials not configured. Message logged only.");
            Log::info("WhatsApp message would be sent to: {$this->certificate->student->mobile}", [
                'message' => $message,
            ]);
            return;
        }

        // Format phone number (remove any non-numeric characters)
        $to = preg_replace('/[^0-9]/', '', $this->certificate->student->mobile);

        // Meta WhatsApp API endpoint
        $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

        // Prepare payload
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => true,
                'body' => $message,
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
     * Prepare the WhatsApp message.
     */
    protected function prepareMessage(): string
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
        $message .= "📥 *Download your certificate:*\n";
        $message .= "{$this->certificate->download_url}\n\n";
        $message .= "🔍 *Verify your certificate:*\n";
        $message .= "{$this->certificate->verification_url}\n\n";
        $message .= "_This link is unique to you and will remain active forever._";

        return $message;
    }
}
