<?php

namespace App\Actions\Prenotazioni;

use App\Models\Prenotazione;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePrenotazione
{
    public function handle(
        int $idUtente,
        int $idTurno,
        string $dataPrenotazione,
        ?string $stato = null,
        bool $forza = false,
    ): Prenotazione {
        return DB::transaction(function () use ($idUtente, $idTurno, $dataPrenotazione, $stato, $forza) {
            if ($forza) {
                // rimuove l'eventuale prenotazione esistente sullo stesso turno/data
                Prenotazione::where('id_turno', $idTurno)
                    ->where('data_prenotazione', $dataPrenotazione)
                    ->delete();
            }

            try {
                return Prenotazione::create([
                    'id_utente' => $idUtente,
                    'id_turno' => $idTurno,
                    'data_prenotazione' => $dataPrenotazione,
                    ...($stato !== null ? ['stato' => $stato] : []),
                ]);
            } catch (QueryException $e) {
                if ((int) $e->errorInfo[1] === 1062) {
                    throw ValidationException::withMessages([
                        'turno' => 'Turno già occupato.',
                    ]);
                }

                throw $e;
            }
        });
    }
}