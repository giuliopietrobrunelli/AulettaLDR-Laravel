<?php

namespace App\Livewire\Admin;

use App\Models\Prenotazione;
use App\Models\Turno;
use App\Models\Utente;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class GestionePrenotazioni extends Component
{
    use WithPagination;

    public bool $modaleAperto = false;
    public bool $modalePrivilegiataAperto = false;
    public ?int $modificaPrenotazioneId = null;

    public string $dataPrenotazione = '';
    public ?int $idTurno = null;
    public ?int $idUtente = null;
    public string $stato = 'non_confermata';

    public string $filtroPeriodo = 'future';
    public string $ricerca = '';

    public function cambiaFiltroPeriodo(string $periodo)
    {
        $this->filtroPeriodo = $periodo;
        $this->resetPage();
        $this->dispatch('modal-aperto');
    }

    public function nextPage($pageName = 'page')
    {
        $this->setPage($this->getPage($pageName) + 1, $pageName);
        $this->dispatch('modal-aperto');
    }

    public function previousPage($pageName = 'page')
    {
        $this->setPage(max($this->getPage($pageName) - 1, 1), $pageName);
        $this->dispatch('modal-aperto');
    }

    public function apriPrenotazionePrivilegiata()
    {
        $this->authorize('create', Prenotazione::class);
        $this->reset(['dataPrenotazione', 'idTurno', 'idUtente', 'modificaPrenotazioneId']);
        $this->stato = 'riservata';
        $this->modalePrivilegiataAperto = true;
        $this->dispatch('modal-aperto');
    }

    public function apriModifica(int $idPrenotazione)
    {
        $prenotazione = Prenotazione::findOrFail($idPrenotazione);
        $this->authorize('update', $prenotazione);

        $this->modificaPrenotazioneId = $prenotazione->id_prenotazione;
        $this->dataPrenotazione = Carbon::parse($prenotazione->data_prenotazione)->format('Y-m-d');
        $this->idTurno = $prenotazione->id_turno;
        $this->idUtente = $prenotazione->id_utente;
        $this->stato = $prenotazione->stato;
        $this->modaleAperto = true;
        $this->dispatch('modal-aperto');
    }

    public function chiudiModale()
    {
        $this->modaleAperto = false;
        $this->modalePrivilegiataAperto = false;
        $this->reset(['dataPrenotazione', 'idTurno', 'idUtente', 'modificaPrenotazioneId']);
    }

    public function salva()
    {
        $this->validate([
            'dataPrenotazione' => 'required|date',
            'idTurno' => 'required|exists:Turno,id_turno',
            'idUtente' => 'required|exists:Utente,id_utente',
            'stato' => 'required|in:non_confermata,confermata,riservata',
        ]);

        try {
            if ($this->modificaPrenotazioneId) {
                $prenotazione = Prenotazione::findOrFail($this->modificaPrenotazioneId);
                $this->authorize('update', $prenotazione);

                $prenotazione->update([
                    'data_prenotazione' => $this->dataPrenotazione,
                    'id_turno' => $this->idTurno,
                    'id_utente' => $this->idUtente,
                    'stato' => $this->stato,
                    'data_conferma' => $this->stato === 'confermata' ? ($prenotazione->data_conferma ?? now()) : null,
                ]);

                $this->dispatch('toast', tipo: 'success', messaggio: 'Prenotazione aggiornata.');
            } else {
                $this->authorize('create', Prenotazione::class);

                Prenotazione::create([
                    'data_prenotazione' => $this->dataPrenotazione,
                    'id_turno' => $this->idTurno,
                    'id_utente' => $this->idUtente,
                    'stato' => $this->stato,
                    'data_creazione_prenotazione' => now(),
                    'data_conferma' => $this->stato === 'confermata' ? now() : null,
                ]);

                $this->dispatch('toast', tipo: 'success', messaggio: 'Prenotazione creata.');
            }

            $this->chiudiModale();
        } catch (\Illuminate\Database\QueryException $e) {
            $this->dispatch('toast', tipo: 'error', messaggio: 'Turno già occupato per questa data.');
        }
    }

    public function elimina()
    {
        $prenotazione = Prenotazione::findOrFail($this->modificaPrenotazioneId);
        $this->authorize('delete', $prenotazione);

        $prenotazione->delete();
        $this->chiudiModale();
        $this->dispatch('toast', tipo: 'success', messaggio: 'Prenotazione eliminata.');
    }

    public function render()
{
    $oggi = Carbon::today()->format('Y-m-d');

    $prenotazioni = Prenotazione::query()
        ->join('Turno', 'Turno.id_turno', '=', 'Prenotazione.id_turno')
        ->select('Prenotazione.*')
        ->with(['turno', 'utente'])
        ->when($this->filtroPeriodo === 'future', fn($q) => $q->where('Prenotazione.data_prenotazione', '>=', $oggi))
        ->when($this->filtroPeriodo === 'passate', fn($q) => $q->where('Prenotazione.data_prenotazione', '<', $oggi))
        ->when($this->ricerca, function ($q) {
            $q->whereHas('utente', function ($q2) {
                $q2->where('nome', 'like', '%' . $this->ricerca . '%')
                    ->orWhere('cognome', 'like', '%' . $this->ricerca . '%');
            });
        })
        ->orderBy('Prenotazione.data_prenotazione', $this->filtroPeriodo === 'passate' ? 'desc' : 'asc')
        ->orderBy('Turno.orario_inizio', $this->filtroPeriodo === 'passate' ? 'desc' : 'asc')
        ->paginate(20);

    $turni = Turno::orderBy('orario_inizio')->get();
    $utenti = Utente::orderBy('cognome')->get();

    return view('livewire.admin.gestione-prenotazioni', [
        'prenotazioni' => $prenotazioni,
        'turni' => $turni,
        'utenti' => $utenti,
    ]);
}
}
