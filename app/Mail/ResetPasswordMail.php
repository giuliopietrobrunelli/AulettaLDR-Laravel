<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nome,
        public string $url,
    ) {}

    public function build()
    {
        return $this->subject('Reimposta la tua password — Auletta LDR')
            ->view('emails.reset-password');
    }
}