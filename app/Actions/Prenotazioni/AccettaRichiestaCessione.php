<?php

namespace App\Actions\Prenotazioni;

use App\Models\RichiestaCessione;
use App\Policies\Concerns\VerificaLimiteSettimanale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AccettaRichiestaCessione
{
    use VerificaLimiteSettimanale;

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

            if (!$this->rispettaLimiteSettimanale($richiesta->id_destinatario, $prenotazione->data_prenotazione)) {
                throw ValidationException::withMessages([
                    'richiesta' => 'Hai già raggiunto il limite di prenotazioni per questa settimana.',
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