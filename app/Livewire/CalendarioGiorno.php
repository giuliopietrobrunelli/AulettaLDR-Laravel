<?php

namespace App\Livewire;

use App\Livewire\Concerns\GestisceModalModificaPrenotazione;
use App\Models\Prenotazione;
use App\Models\Turno;
use Illuminate\Support\Carbon;
use Livewire\Component;

class CalendarioGiorno extends Component
{
    use GestisceModalModificaPrenotazione;

    public string $data;

    public function mount(string $data)
    {
        $this->data = $data;
    }

    public ?int $prenotazioneConfermataId = null;
    public bool $prenotazioneAutoConfermata = false;

    public function prenota(int $idTurno)
    {
        $utente = auth()->user()->utente;

        $policy = new \App\Policies\PrenotazionePolicy();
        if (!$policy->creaComeUtenteNormale(auth()->user(), $this->data)) {
            $this->dispatch('toast', tipo: 'error', messaggio: 'Non hai i requisiti per effettuare questa prenotazione.');
            return;
        }

        $turno = Turno::findOrFail($idTurno);
        $dataStr = Carbon::parse($this->data)->format('Y-m-d');
        $inizioTurno = Carbon::parse($dataStr . ' ' . $turno->orario_inizio);
        $finestraConfermaChiusa = $inizioTurno->copy()->addMinutes(30)->isPast();

        $stato = $finestraConfermaChiusa ? 'confermata' : 'non_confermata';
        $dataConferma = $finestraConfermaChiusa ? now() : null;

        try {
            $prenotazione = Prenotazione::create([
                'id_turno' => $idTurno,
                'id_utente' => $utente->id_utente,
                'data_prenotazione' => $this->data,
                'stato' => $stato,
                'data_creazione_prenotazione' => now(),
                'data_conferma' => $dataConferma,
            ]);

            $this->prenotazioneConfermataId = $prenotazione->id_prenotazione;
            $this->prenotazioneAutoConfermata = $finestraConfermaChiusa;
            $this->dispatch('prenotazione-aggiornata');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->dispatch('toast', tipo: 'error', messaggio: 'Questo turno è già stato prenotato.');
        }
    }

    public function chiudiModalSuccesso()
    {
        $this->prenotazioneConfermataId = null;
        $this->prenotazioneAutoConfermata = false;
    }

    public function giornoPrecedente()
    {
        $data = Carbon::parse($this->data)->subDay();
        $this->redirect(route('calendario.giorno', ['data' => $data->format('Y-m-d')]));
    }

    public function giornoSuccessivo()
    {
        $data = Carbon::parse($this->data)->addDay();
        $this->redirect(route('calendario.giorno', ['data' => $data->format('Y-m-d')]));
    }

    public function giornoOggi()
    {
        $this->redirect(route('calendario.giorno', ['data' => Carbon::today()->format('Y-m-d')]));
    }

    public function tornaAlMese()
    {
        $this->redirect(route('calendario.mese'));
    }

    public function render()
    {
        Carbon::setLocale('it');
        $dataCarbon = Carbon::parse($this->data);

        $turni = Turno::where('attivo', true)->orderBy('orario_inizio')->get();

        $prenotazioni = Prenotazione::where('data_prenotazione', $dataCarbon->format('Y-m-d'))
            ->with('utente:id_utente,nome,cognome')
            ->get()
            ->keyBy('id_turno');

        $miaUtenteId = auth()->user()->utente?->id_utente;

        $righe = $turni->map(function ($turno) use ($prenotazioni, $dataCarbon, $miaUtenteId) {
            $prenotazione = $prenotazioni->get($turno->id_turno);

            $fineTurno = Carbon::parse($dataCarbon->format('Y-m-d') . ' ' . $turno->orario_fine);
            $turnoPassato = $fineTurno->isPast();

            $stato = 'available';
            if ($prenotazione) {
                $stato = ($prenotazione->id_utente === $miaUtenteId) ? 'own' : 'occupied';
            } elseif ($turnoPassato) {
                $stato = 'past';
            }

            return [
                'turno' => $turno,
                'prenotazione' => $prenotazione,
                'stato' => $stato,
                'passato' => $turnoPassato,
            ];
        });

        if ($dataCarbon->isToday()) {
            $titoloGiorno = 'Oggi, ' . $dataCarbon->translatedFormat('j F');
        } elseif ($dataCarbon->isTomorrow()) {
            $titoloGiorno = 'Domani, ' . $dataCarbon->translatedFormat('j F');
        } else {
            $titoloGiorno = ucfirst($dataCarbon->translatedFormat('l j F'));
        }

        $prenotazioneConfermata = $this->prenotazioneConfermataId
            ? Prenotazione::with('turno')->find($this->prenotazioneConfermataId)
            : null;

        return view('livewire.calendario-giorno', array_merge([
            'righe' => $righe,
            'data' => $dataCarbon,
            'titoloGiorno' => $titoloGiorno,
            'prenotazioneConfermata' => $prenotazioneConfermata,
        ], $this->datiModal($miaUtenteId)));
    }
}