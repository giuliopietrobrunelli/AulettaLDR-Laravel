<?php

namespace App\Actions\RichiesteCessione;

use App\Models\Prenotazione;
use App\Models\RichiestaCessione;
use Illuminate\Validation\ValidationException;

class CediPrenotazione
{
    public function handle(Prenotazione $prenotazione, int $idDestinatario): RichiestaCessione
    {
        $richiestaAttiva = RichiestaCessione::where('id_prenotazione', $prenotazione->id_prenotazione)
            ->where('stato', 'in_attesa')
            ->exists();

        if ($richiestaAttiva) {
            throw ValidationException::withMessages([
                'prenotazione' => 'È già presente una richiesta attiva per questa prenotazione.',
            ]);
        }

        return RichiestaCessione::create([
            'id_prenotazione' => $prenotazione->id_prenotazione,
            'id_mittente' => $prenotazione->id_utente,
            'id_destinatario' => $idDestinatario,
        ]);
    }
}