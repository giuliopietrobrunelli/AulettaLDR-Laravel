<?php

namespace App\Actions\Auth;

use App\Mail\VerificationCodeMail;
use App\Models\Utente;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class GenerateVerificationCode
{
    public function handle(Utente $utente): void
    {
        // invalida eventuali codici precedenti ancora attivi per questo utente
        $utente->verificationCodes()
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $plainCode = (string) random_int(100000, 999999);

        $utente->verificationCodes()->create([
            'code' => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($utente->email)->send(
            new VerificationCodeMail($utente->nome, $plainCode)
        );
    }
}