<?php

namespace App\Livewire;

use App\Models\Prenotazione;
use App\Models\Turno;
use Illuminate\Support\Carbon;
use Livewire\Component;

class CalendarioMese extends Component
{
    public int $offsetMese = 0; // 0 = mese corrente, -1 = precedente, 1 = successivo

    public function mount()
    {
        // nessun dato da inizializzare qui, tutto calcolato nei computed
    }

    public function meseVista()
    {
        return Carbon::now()->startOfMonth()->addMonthsNoOverflow($this->offsetMese);
    }

    public function puoVedereMeseSuccessivo(): bool
    {
        if ($this->offsetMese >= 1) {
            return false;
        }

        $settimaneAnticipo = (int) \App\Models\Impostazioni::where('nome', 'settimane_anticipo')->value('valore');
        $inizioMeseSuccessivo = Carbon::now()->startOfMonth()->addMonthNoOverflow();
        $soglia = $inizioMeseSuccessivo->copy()->subWeeks($settimaneAnticipo);

        return Carbon::now()->gte($soglia);
    }

    public function meseIndietro()
    {
        if ($this->offsetMese > -1) {
            $this->offsetMese--;
        }
    }

    public function meseAvanti()
    {
        if ($this->offsetMese === 0 && $this->puoVedereMeseSuccessivo()) {
            $this->offsetMese = 1;
        } elseif ($this->offsetMese === -1) {
            $this->offsetMese = 0;
        }
    }

    public function meseOggi()
    {
        $this->offsetMese = 0;
    }

    public function vaiAlGiorno($data)
    {
        $this->redirect(route('calendario.giorno', ['data' => $data]));
    }

    public function render()
    {
        Carbon::setLocale('it');
        $mese = $this->meseVista();
        $inizioGriglia = $mese->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $fineGriglia = $mese->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $prenotazioni = Prenotazione::whereBetween('data_prenotazione', [$inizioGriglia, $fineGriglia])
            ->whereHas('turno', fn($q) => $q->where('attivo', true))
            ->with('utente:id_utente,nome,cognome')
            ->get()
            ->groupBy(fn($p) => Carbon::parse($p->data_prenotazione)->format('Y-m-d'));

        $settimaneAnticipo = (int) \App\Models\Impostazioni::where('nome', 'settimane_anticipo')->value('valore');
        $inizioMeseSuccessivo = Carbon::now()->startOfMonth()->addMonthNoOverflow();
        $sogliaVisibilita = $inizioMeseSuccessivo->copy()->subWeeks($settimaneAnticipo);

        $giorni = collect();
        $cursore = $inizioGriglia->copy();
        while ($cursore->lte($fineGriglia)) {
            $chiave = $cursore->format('Y-m-d');

            $nelMese = $cursore->month === $mese->month;
            $passato = $cursore->isBefore(Carbon::today());
            // "locked": è nel mese successivo a quello corrente reale, e non ancora nella finestra di visibilità
            $locked = !$passato && $cursore->isAfter(Carbon::now()->endOfMonth()) && Carbon::now()->lt($sogliaVisibilita) && $cursore->month !== Carbon::now()->month;

            $giorni->push([
                'data' => $cursore->copy(),
                'nel_mese' => $nelMese,
                'oggi' => $cursore->isToday(),
                'passato' => $passato,
                'locked' => $locked,
                'prenotazioni' => $prenotazioni->get($chiave, collect()),
            ]);
            $cursore->addDay();
        }

        return view('livewire.calendario-mese', [
            'giorni' => $giorni,
            'titoloMese' => $mese->translatedFormat('F Y'),
            'puoIndietro' => $this->offsetMese > -1,
            'puoAvanti' => $this->offsetMese === -1 || ($this->offsetMese === 0 && $this->puoVedereMeseSuccessivo()),
        ]);
    }
}