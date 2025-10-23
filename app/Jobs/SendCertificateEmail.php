<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Mail\CertificateIssuedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

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
        $this->certificate->load(['student', 'school', 'event']);

        // Send email to student
        if ($this->certificate->student->email) {
            Mail::to($this->certificate->student->email)
                ->send(new CertificateIssuedMail($this->certificate));

            // Update certificate to mark email as sent
            $this->certificate->update(['sent_via_email' => true]);
        }
    }
}
