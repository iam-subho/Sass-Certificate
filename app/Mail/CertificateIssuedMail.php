<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CertificateIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate;
    protected $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Certificate $certificate, string $pdfPath = null)
    {
        $this->certificate = $certificate;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Certificate is Ready - ' . $this->certificate->school->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate-issued',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->pdfPath && Storage::exists($this->pdfPath)) {
            return [
                Attachment::fromStorage($this->pdfPath)
                    ->as('certificate-' . $this->certificate->certificate_id . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
