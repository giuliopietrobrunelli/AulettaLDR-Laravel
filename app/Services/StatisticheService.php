<?php

namespace App\Services;

use App\Models\Impostazioni;
use App\Models\Prenotazione;
use App\Models\Utente;
use Illuminate\Support\Carbon;

class StatisticheService
{
    public function riepilogo(): array
    {
        return [
            'utenti_attivi' => $this->utentiAttivi(),
            'utenti_totali' => $this->utentiTotali(),
            'prenotazioni_mese_corrente' => $this->prenotazioniMeseCorrente(),
            'prenotazioni_oggi' => $this->prenotazioniOggi(),
            'tasso_conferma' => $this->tassoConferma(),
            'limite_settimanale' => $this->limiteSettimanale(),
        ];
    }

    public function utentiAttivi(): int
    {
        return Utente::where('registrato', true)->count();
    }

    public function utentiTotali(): int
    {
        return Utente::count();
    }

    public function prenotazioniMeseCorrente(): int
    {
        $inizio = Carbon::now()->startOfMonth();
        $fine = Carbon::now()->endOfMonth();

        return Prenotazione::whereBetween('data_prenotazione', [$inizio, $fine])->count();
    }

    public function prenotazioniOggi(): int
    {
        return Prenotazione::whereDate('data_prenotazione', Carbon::today())->count();
    }

    public function tassoConferma(): float
    {
        $totali = Prenotazione::count();

        if ($totali === 0) {
            return 0.0;
        }

        $confermate = Prenotazione::where('stato', 'confermata')->count();

        return round(($confermate / $totali) * 100, 2);
    }

    public function limiteSettimanale(): int
    {
        return (int) Impostazioni::where('nome', 'limite_settimanale')->value('valore');
    }
}