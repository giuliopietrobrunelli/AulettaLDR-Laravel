<?php

namespace App\Http\Controllers;

use App\Models\Impostazioni;
use App\Models\Prenotazione;
use App\Models\Utente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class StatisticheController extends Controller
{
    public function index()
    {
        if (! Gate::allows('viewStatistiche')) {
        abort(403);
    }

        $oggi = Carbon::now();
        $inizioMese = $oggi->copy()->startOfMonth()->toDateString();
        $fineMese = $oggi->copy()->endOfMonth()->toDateString();

        $totaliUtenti = Utente::count();
        $registrati = Utente::where('registrato', true)->count();

        $prenotazioniMese = Prenotazione::whereBetween('data_prenotazione', [$inizioMese, $fineMese])->get();
        $prenotazioniOggi = Prenotazione::where('data_prenotazione', $oggi->toDateString())->count();

        $confermate = $prenotazioniMese->filter(
            fn ($p) => $p->stato === 'confermata' || $p->data_conferma !== null
        )->count();

        $tasso = $prenotazioniMese->count()
            ? round($confermate / $prenotazioniMese->count() * 100)
            : 0;

        return response()->json([
            'utenti_totali' => $totaliUtenti,
            'utenti_registrati' => $registrati,
            'prenotazioni_mese' => $prenotazioniMese->count(),
            'prenotazioni_oggi' => $prenotazioniOggi,
            'tasso_conferma' => $tasso,
            'limite_settimanale' => (int) Impostazioni::where('nome', 'limite_settimanale')->value('valore'),
            'mese_label' => $oggi->translatedFormat('F Y'),
        ]);
    }
}