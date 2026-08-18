<?php

namespace App\Policies\Concerns;

use App\Models\Impostazioni;
use App\Models\Prenotazione;
use Illuminate\Support\Carbon;

trait VerificaLimiteSettimanale
{
    protected function rispettaLimiteSettimanale (int $idUtente, string $dataPrenotazione): bool
    {
        $limite = (int) Impostazioni::where('nome', 'limite_settimanale')->value('valore');
        $data = Carbon::parse($dataPrenotazione);

        $inizioSettimana = $data->copy()->startOfWeek();
        $fineSettimana = $data->copy()->endOfWeek();

        $conteggio = Prenotazione::where('id_utente', $idUtente)
            ->whereBetween('data_prenotazione', [$inizioSettimana, $fineSettimana])
            ->count();

        return $conteggio < $limite;

    }
}
