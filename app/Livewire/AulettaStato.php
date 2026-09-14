<?php

namespace App\Livewire;

use App\Models\Prenotazione;
use App\Models\Turno;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AulettaStato extends Component
{
    public function render()
    {
        $ora = Carbon::now();
        $oggi = $ora->format('Y-m-d');

        // trova il turno che copre l'orario attuale, attivo o meno
        $turnoCorrente = Turno::all()->first(function ($turno) use ($ora) {
            $inizio = Carbon::parse($turno->orario_inizio);
            $fine = Carbon::parse($turno->orario_fine);
            $adesso = Carbon::createFromTime($ora->hour, $ora->minute, $ora->second);

            return $adesso->between($inizio, $fine);
        });

        $stato = 'available'; // available | occupied | mine | unavailable
        $prenotazioneAttuale = null;

        if (!$turnoCorrente) {
            $stato = 'available';
        } elseif (!$turnoCorrente->attivo) {
            $stato = 'unavailable';
        } else {
            $prenotazioneAttuale = Prenotazione::where('id_turno', $turnoCorrente->id_turno)
                ->where('data_prenotazione', $oggi)
                ->first();

            if ($prenotazioneAttuale) {
                $miaUtenteId = auth()->user()->utente?->id_utente;
                $stato = $prenotazioneAttuale->id_utente === $miaUtenteId ? 'mine' : 'occupied';
            } else {
                $stato = 'available';
            }
        }

        return view('livewire.auletta-stato', [
            'stato' => $stato,
        ]);
    }
}