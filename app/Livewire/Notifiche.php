<?php

namespace App\Livewire;

use App\Actions\Prenotazioni\AccettaRichiestaCessione;
use App\Models\RichiestaCessione;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class Notifiche extends Component
{
    #[On('prenotazione-aggiornata')]
    public function aggiorna()
    {
        // vuoto: la presenza del metodo triggera un re-render con dati freschi
    }

    public function accetta(int $idRichiesta, AccettaRichiestaCessione $accettaRichiesta)
    {
        $richiesta = RichiestaCessione::findOrFail($idRichiesta);
        $this->authorize('accept', $richiesta);

        try {
            $accettaRichiesta->handle($richiesta);
            $this->dispatch('toast', tipo: 'success', messaggio: 'Turno accettato.');
            $this->dispatch('prenotazione-aggiornata');
        } catch (ValidationException $e) {
            $this->dispatch('toast', tipo: 'error', messaggio: $e->getMessage());
        }
    }

    public function rifiuta(int $idRichiesta)
    {
        $richiesta = RichiestaCessione::findOrFail($idRichiesta);
        $this->authorize('reject', $richiesta);

        $richiesta->update(['stato' => 'rifiutata']);

        $this->dispatch('toast', tipo: 'success', messaggio: 'Richiesta rifiutata.');
    }

    public function render()
    {
        $utente = auth()->user()->utente;

        $richieste = RichiestaCessione::with([
                'prenotazione.turno',
                'mittente:id_utente,nome,cognome',
            ])
            ->where('id_destinatario', $utente?->id_utente)
            ->where('stato', 'in_attesa')
            ->latest('created_at')
            ->get();

        return view('livewire.notifiche', [
            'richieste' => $richieste,
        ]);
    }
}