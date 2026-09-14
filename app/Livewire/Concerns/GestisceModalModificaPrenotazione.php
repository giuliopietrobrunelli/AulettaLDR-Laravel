<?php

namespace App\Livewire\Concerns;

use App\Models\Prenotazione;
use App\Models\RichiestaCessione;
use App\Models\Utente;

trait GestisceModalModificaPrenotazione
{
    public ?int $modalPrenotazioneId = null;
    public ?int $destinatarioId = null;

    public function apriModal(int $idPrenotazione)
    {
        $this->modalPrenotazioneId = $idPrenotazione;
        $this->destinatarioId = null;
    }

    public function chiudiModal()
    {
        $this->modalPrenotazioneId = null;
        $this->destinatarioId = null;
    }

    public function cediTurno()
    {
        $prenotazione = Prenotazione::findOrFail($this->modalPrenotazioneId);
        $destinatario = Utente::findOrFail($this->destinatarioId);

        $this->authorize('create', [RichiestaCessione::class, $prenotazione, $destinatario]);

        RichiestaCessione::create([
            'id_prenotazione' => $prenotazione->id_prenotazione,
            'id_mittente' => auth()->user()->utente->id_utente,
            'id_destinatario' => $destinatario->id_utente,
            'stato' => 'in_attesa',
        ]);

        $this->chiudiModal();
        $this->dispatch('toast', tipo: 'success', messaggio: 'Richiesta di cessione inviata.');
        $this->dispatch('prenotazione-aggiornata');
    }

    public function avvisaConfermata()
    {
        $this->dispatch('toast', tipo: 'info', messaggio: 'Prenotazione già confermata, non è modificabile.');
    }

    public function rinunciaTurno()
    {
        $prenotazione = Prenotazione::findOrFail($this->modalPrenotazioneId);
        $this->authorize('delete', $prenotazione);

        $prenotazione->delete();
        $this->chiudiModal();
        $this->dispatch('toast', tipo: 'success', messaggio: 'Prenotazione annullata.');
        $this->dispatch('prenotazione-aggiornata');
    }

    protected function datiModal(?int $miaUtenteId): array
    {
        $prenotazioneModal = $this->modalPrenotazioneId
            ? Prenotazione::find($this->modalPrenotazioneId)
            : null;

        $utentiCedibili = collect();
        if ($prenotazioneModal) {
            $utentiCedibili = Utente::where('cauzione', true)
                ->where('registrato', true)
                ->where('id_utente', '!=', $miaUtenteId)
                ->orderBy('cognome')
                ->get();
        }

        return compact('prenotazioneModal', 'utentiCedibili');
    }
}