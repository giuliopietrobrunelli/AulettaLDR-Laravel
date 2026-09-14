<?php

namespace App\Livewire;

use App\Models\Impostazioni;
use App\Models\Prenotazione;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class RiepilogoSettimanale extends Component
{
    #[On('prenotazione-aggiornata')]
    public function aggiorna()
    {
        // vuoto: la presenza del metodo triggera un re-render con dati freschi
    }

    public function render()
    {
        $utente = auth()->user()->utente;
        $miaUtenteId = $utente?->id_utente;

        $limiteSettimanale = (int) Impostazioni::where('nome', 'limite_settimanale')->value('valore');

        $meseCorrente = Carbon::now()->startOfMonth();
        $inizioGriglia = $meseCorrente->copy()->startOfWeek(Carbon::MONDAY);
        $fineGriglia = $meseCorrente->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $mieprenotazioni = Prenotazione::where('id_utente', $miaUtenteId)
            ->whereBetween('data_prenotazione', [$inizioGriglia, $fineGriglia])
            ->get();

        $settimane = [];
        $cursore = $inizioGriglia->copy();
        $settimanaCorrenteIndex = null;
        $i = 0;

        while ($cursore->lte($fineGriglia)) {
            $inizioSettimana = $cursore->copy();
            $fineSettimana = $cursore->copy()->addDays(6);

            $conteggio = $mieprenotazioni->filter(function ($p) use ($inizioSettimana, $fineSettimana) {
                $data = Carbon::parse($p->data_prenotazione);
                return $data->between($inizioSettimana, $fineSettimana);
            })->count();

            $èSettimanaCorrente = Carbon::now()->between($inizioSettimana, $fineSettimana);
            $èPassata = $fineSettimana->isPast();

            if ($èSettimanaCorrente) {
                $settimanaCorrenteIndex = $i;
            }

            $settimane[] = [
                'inizio' => $inizioSettimana,
                'fine' => $fineSettimana,
                'conteggio' => $conteggio,
                'corrente' => $èSettimanaCorrente,
                'passata' => $èPassata,
            ];

            $cursore->addDays(7);
            $i++;
        }

        $conteggioAttuale = $settimanaCorrenteIndex !== null
            ? $settimane[$settimanaCorrenteIndex]['conteggio']
            : 0;

        return view('livewire.riepilogo-settimanale', [
            'settimane' => $settimane,
            'limiteSettimanale' => $limiteSettimanale,
            'conteggioAttuale' => $conteggioAttuale,
        ]);
    }
}