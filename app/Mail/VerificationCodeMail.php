<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nome,
        public string $code,
    ) {}

    public function build()
    {
        return $this->subject('Il tuo codice di verifica — Auletta LDR')
            ->view('emails.verification-code');
    }
}