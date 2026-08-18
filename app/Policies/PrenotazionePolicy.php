<?php

namespace App\Policies;

use App\Models\Impostazioni;
use App\Models\Prenotazione;
use App\Models\User;
use App\Policies\Concerns\AutorizzaAdmin;
use App\Policies\Concerns\VerificaLimiteSettimanale;
use Illuminate\Support\Carbon;

class PrenotazionePolicy
{
    use AutorizzaAdmin, VerificaLimiteSettimanale;

    public function create(User $user, ?string $dataPrenotazione = null): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        $utente = $user->utente;

        if (!$utente || !$utente->cauzione || !$utente->registrato) {
            return false;
        }

        if ($dataPrenotazione === null) {
            return true;
        }

        return $this->rispettaFinestraVisibilita($dataPrenotazione)
            && $this->rispettaLimiteSettimanale($utente->id_utente, $dataPrenotazione);
    }

    public function update(User $user, Prenotazione $prenotazione): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Prenotazione $prenotazione): bool
    {
        if ($prenotazione->stato === 'confermata') {
            return false;
        }

        $utente = $user->utente;

        if (!$utente) {
            return false;
        }

        $isAdmin = $this->isAdmin($user);

        if ($prenotazione->stato === 'riservata') {
            return $isAdmin;
        }

        return $isAdmin || $prenotazione->id_utente === $utente->id_utente;
    }

    private function rispettaFinestraVisibilita(string $dataPrenotazione): bool
    {
        $data = Carbon::parse($dataPrenotazione);
        $oggi = Carbon::now();
        $settimaneAnticipo = (int) Impostazioni::where('nome', 'settimane_anticipo')->value('valore');

        $meseCorrente = $oggi->isSameMonth($data);
        $meseSuccessivo = $oggi->copy()->addMonthNoOverflow()->isSameMonth($data);

        if ($meseCorrente) {
            return true;
        }

        if ($meseSuccessivo) {
            $inizioVisibilita = $data->copy()->startOfMonth()->subWeeks($settimaneAnticipo);
            return $oggi->gte($inizioVisibilita);
        }

        return false;
    }
}