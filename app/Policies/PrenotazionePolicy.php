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

        return $this->creaComeUtenteNormale($user, $dataPrenotazione);
    }

    public function creaComeUtenteNormale(User $user, ?string $dataPrenotazione = null): bool
    {
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

    public function confirm(User $user, Prenotazione $prenotazione): bool
    {
        $utente = $user->utente;

        if (!$utente) {
            return false;
        }

        return $prenotazione->id_utente === $utente->id_utente
            && $prenotazione->stato === 'non_confermata';
    }

    public function delete(User $user, Prenotazione $prenotazione): bool
    {
        $utente = $user->utente;

        if (!$utente) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return true; // l'admin cancella qualsiasi prenotazione, indipendentemente dallo stato
        }

        // un socio normale non può mai cancellare una prenotazione confermata o riservata (non sua)
        if (in_array($prenotazione->stato, ['confermata', 'riservata'])) {
            return false;
        }

        return $prenotazione->id_utente === $utente->id_utente;
    }

    private function rispettaFinestraVisibilita(string $dataPrenotazione): bool
    {
        $data = Carbon::parse($dataPrenotazione);
        $oggi = Carbon::now();

        if ($data->isBefore($oggi->copy()->startOfDay())) {
            return false; // niente prenotazioni nel passato
        }

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