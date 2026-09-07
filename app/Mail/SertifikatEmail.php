<?php

namespace App\Mail;

use App\Mail\Concerns\LogsEmailStatus;
use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\RateLimited;

class SertifikatEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, LogsEmailStatus;
    public $tries = 1;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $certificate;

    public function middleware()
    {
        return [new RateLimited('emails')];
    }

    public function __construct(Certificate $certificate)
    {
        // Menyimpan data sertifikat ke properti class
        $this->certificate = $certificate;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
          subject: 'Sertifikat Anda: ' . $this->certificate->body,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
   public function build()
    {
        return $this->subject('Sertifikat Anda: ' . $this->certificate->body)
                    ->view('Emails.Certificate') // Nama file blade yang sudah dibuat sebelumnya
                    ->with([
                        'name' => $this->certificate->name,
                        'eventTitle' => $this->certificate->body,
                        'certificateLink' => $this->certificate->link,
                    ]);
    }
    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
