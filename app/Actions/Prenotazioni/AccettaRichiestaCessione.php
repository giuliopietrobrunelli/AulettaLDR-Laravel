<?php

namespace App\Actions\RichiesteCessione;

use App\Models\RichiestaCessione;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AccettaRichiestaCessione
{
    public function handle(RichiestaCessione $richiesta): void
    {
        DB::transaction(function () use ($richiesta) {
            $prenotazione = $richiesta->prenotazione()->with('turno')->lockForUpdate()->first();

            if (!$prenotazione || $prenotazione->id_utente !== $richiesta->id_mittente) {
                $richiesta->update(['stato' => 'scaduta']);

                throw ValidationException::withMessages([
                    'richiesta' => 'Il turno non appartiene più a chi te lo ha ceduto.',
                ]);
            }

            $turno = $prenotazione->turno;
            $fineTurno = Carbon::parse($prenotazione->data_prenotazione)
                ->setTimeFromTimeString($turno->orario_fine);

            if ($fineTurno->isPast()) {
                $richiesta->update(['stato' => 'scaduta']);

                throw ValidationException::withMessages([
                    'richiesta' => 'Il turno richiesto è già terminato.',
                ]);
            }

            $prenotazione->update([
                'id_utente' => $richiesta->id_destinatario,
                'stato' => 'non_confermata',
                'data_conferma' => null,
            ]);

            $richiesta->update(['stato' => 'accettata']);
        });
    }
}