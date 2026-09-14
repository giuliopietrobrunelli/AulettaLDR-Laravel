<?php

namespace App\Livewire;

use App\Livewire\Concerns\GestisceModalModificaPrenotazione;
use App\Models\Prenotazione;
use Illuminate\Support\Carbon;
use Livewire\Component;

class LeMiePrenotazioni extends Component
{
    use GestisceModalModificaPrenotazione;

    public function confermaPresenza(int $idPrenotazione)
    {
        $prenotazione = Prenotazione::findOrFail($idPrenotazione);

        $utente = auth()->user()->utente;
        if (!$utente || $prenotazione->id_utente !== $utente->id_utente) {
            abort(403);
        }

        $prenotazione->update([
            'stato' => 'confermata',
            'data_conferma' => now(),
        ]);

        $this->dispatch('toast', tipo: 'success', messaggio: 'Presenza confermata.');
        $this->dispatch('prenotazione-aggiornata');
    }

    public function render()
    {
        Carbon::setLocale('it');

        $utente = auth()->user()->utente;
        $miaUtenteId = $utente?->id_utente;

        $oggi = Carbon::today();
        $adesso = Carbon::now();

        $turnoCorrente = Prenotazione::whereDate('data_prenotazione', $oggi)
            ->with(['turno', 'utente'])
            ->get()
            ->first(function ($p) use ($adesso, $oggi) {
                if (!$p->turno) {
                    return false;
                }
                $inizio = Carbon::parse($oggi->format('Y-m-d') . ' ' . $p->turno->orario_inizio);
                $fine = Carbon::parse($oggi->format('Y-m-d') . ' ' . $p->turno->orario_fine);
                return $adesso->between($inizio, $fine);
            });

        $mieFuture = Prenotazione::where('id_utente', $miaUtenteId)
            ->with('turno')
            ->get()
            ->filter(function ($p) use ($turnoCorrente) {
                if ($turnoCorrente && $p->id_prenotazione === $turnoCorrente->id_prenotazione) {
                    return false;
                }
                if (!$p->turno) {
                    return false;
                }
                $dataStr = Carbon::parse($p->data_prenotazione)->format('Y-m-d');
                $inizio = Carbon::parse($dataStr . ' ' . $p->turno->orario_inizio);
                return $inizio->isFuture();
            })
            ->sortBy(fn ($p) => $p->data_prenotazione . $p->turno->orario_inizio)
            ->groupBy(fn ($p) => Carbon::parse($p->data_prenotazione)->format('Y-m-d'));

        return view('livewire.le-mie-prenotazioni', array_merge([
            'turnoCorrente' => $turnoCorrente,
            'mieFuture' => $mieFuture,
            'miaUtenteId' => $miaUtenteId,
        ], $this->datiModal($miaUtenteId)));
    }
}