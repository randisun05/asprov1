<?php

namespace App\Mail;

use App\Mail\Concerns\LogsEmailStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailAprrove extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, LogsEmailStatus;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {       
        return $this->subject('No-Reply-Registrasi Keanggotaan Aspro')
                    ->view('Emails.Approve');
    }

}
