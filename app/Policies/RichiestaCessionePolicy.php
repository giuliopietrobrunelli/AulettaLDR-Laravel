<?php

namespace App\Policies;

use App\Models\Prenotazione;
use App\Models\RichiestaCessione;
use App\Models\User;
use App\Models\Utente;
use App\Policies\Concerns\AutorizzaAdmin;
use App\Policies\Concerns\VerificaLimiteSettimanale;

class RichiestaCessionePolicy
{
    use AutorizzaAdmin, VerificaLimiteSettimanale;

    public function create(User $user, Prenotazione $prenotazione, Utente $destinatario): bool
    {
        $mittenteUtente = $user->utente;

        if (!$mittenteUtente) {
            return false;
        }

        if ($prenotazione->id_utente !== $mittenteUtente->id_utente) {
            return false;
        }

        if (!in_array($prenotazione->stato, ['confermata', 'non_confermata'])) {
            return false;
        }

        if ($destinatario->id_utente === $mittenteUtente->id_utente) {
            return false;
        }

        if (!$destinatario->cauzione || !$destinatario->registrato) {
            return false;
        }

        // non può aprirne una nuova se ce n'è già una in attesa per questa prenotazione
        $esisteInAttesa = RichiestaCessione::where('id_prenotazione', $prenotazione->id_prenotazione)
            ->where('stato', 'in_attesa')
            ->exists();

        if ($esisteInAttesa) {
            return false;
        }

        return $this->rispettaLimiteSettimanale($destinatario->id_utente, $prenotazione->data_prenotazione);
    }

    public function accept(User $user, RichiestaCessione $richiesta): bool
    {
        $utente = $user->utente;

        return $utente !== null
            && $richiesta->id_destinatario === $utente->id_utente
            && $richiesta->stato === 'in_attesa';
    }

    public function reject(User $user, RichiestaCessione $richiesta): bool
    {
        return $this->accept($user, $richiesta); // stessa condizione di accept
    }
}