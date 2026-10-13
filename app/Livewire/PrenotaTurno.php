<?php

namespace App\Livewire;

use App\Actions\Prenotazioni\CreatePrenotazione;
use App\Models\Impostazioni;
use App\Models\Prenotazione;
use App\Models\Turno;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PrenotaTurno extends Component
{
    public string $data = '';
    public ?int $idTurno = null;

    public function mount()
    {
        $this->data = Carbon::today()->format('Y-m-d');
    }

    public function updatedData()
    {
        $this->idTurno = null;
    }

    public function dataMinima(): string
    {
        return Carbon::today()->format('Y-m-d');
    }

    public function dataMassima(): string
    {
        $settimaneAnticipo = (int) Impostazioni::where('nome', 'settimane_anticipo')->value('valore');
        $inizioMeseSuccessivo = Carbon::now()->startOfMonth()->addMonthNoOverflow();
        $sogliaVisibilita = $inizioMeseSuccessivo->copy()->subWeeks($settimaneAnticipo);

        if (Carbon::now()->lt($sogliaVisibilita)) {
            return Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        return $inizioMeseSuccessivo->copy()->endOfMonth()->format('Y-m-d');
    }

    public function turniDelGiorno()
    {
        if (!$this->data) {
            return collect();
        }

        $turni = Turno::where('attivo', true)->orderBy('orario_inizio')->get();

        $occupati = Prenotazione::where('data_prenotazione', $this->data)
            ->pluck('id_turno')
            ->all();

        return $turni->map(function ($turno) use ($occupati) {
            $turno->occupato = in_array($turno->id_turno, $occupati);
            $turno->terminato = $turno->terminatoIl($this->data);
            return $turno;
        });
    }

    public function salva(CreatePrenotazione $createPrenotazione)
{
    if (!$this->idTurno) {
        $this->dispatch('toast', tipo: 'error', messaggio: 'Seleziona un turno.');
        return;
    }

    $turno = Turno::where('attivo', true)->find($this->idTurno);

    if (!$turno) {
        $this->dispatch('toast', tipo: 'error', messaggio: 'Turno non disponibile.');
        return;
    }

    if ($turno->terminatoIl($this->data)) {
        $this->dispatch('toast', tipo: 'error', messaggio: 'Il turno è già terminato.');
        return;
    }

    // turno iniziato da più di 30 minuti: prenotazione confermata in automatico
    $inizioTurno = Carbon::parse(Carbon::parse($this->data)->format('Y-m-d') . ' ' . $turno->orario_inizio);
    $autoConfermata = $inizioTurno->copy()->addMinutes(30)->isPast();

    $utente = auth()->user()->utente;

    try {
        $this->authorize('create', [Prenotazione::class, $this->data]);

        $createPrenotazione->handle(
            $utente->id_utente,
            $this->idTurno,
            $this->data,
            $autoConfermata ? 'confermata' : null,
            false,
            $autoConfermata ? now() : null,
        );

        $this->dispatch('toast', tipo: 'success', messaggio: $autoConfermata
            ? 'Prenotazione effettuata. Il turno era già iniziato da più di 30 minuti: è stata confermata automaticamente e non è più annullabile.'
            : 'Prenotazione effettuata con successo.');
        $this->dispatch('prenotazione-aggiornata');
        $this->reset(['idTurno']);
        $this->dispatch('close-modal-prenota');
    } catch (ValidationException $e) {
        $this->dispatch('toast', tipo: 'error', messaggio: collect($e->errors())->flatten()->first() ?? 'Non è stato possibile completare la prenotazione.');
    } catch (AuthorizationException $e) {
        $this->dispatch('toast', tipo: 'error', messaggio: 'Non puoi effettuare questa prenotazione: verifica la data scelta o il limite settimanale.');
    }
}

    public function render()
    {
        Carbon::setLocale('it');

        return view('livewire.prenota-turno', [
            'turni' => $this->turniDelGiorno(),
            'dataMinima' => $this->dataMinima(),
            'dataMassima' => $this->dataMassima(),
        ]);
    }
}
