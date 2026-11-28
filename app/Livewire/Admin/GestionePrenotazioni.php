<?php

namespace App\Livewire\Admin;

use App\Models\Prenotazione;
use App\Models\Turno;
use App\Models\Utente;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class GestionePrenotazioni extends Component
{
    use WithPagination;

    public bool $modaleAperto = false;
    public bool $modalePrivilegiataAperto = false;
    public ?int $modificaPrenotazioneId = null;

    public string $dataPrenotazione = '';
    public ?int $idTurno = null;
    public ?int $idUtente = null;
    public string $stato = '';

    public string $filtroPeriodo = 'future';
    public string $ricerca = '';


    public function mount()
    {
        $this->dataPrenotazione = now()->format('Y-m-d');
    }

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
        $this->authorize('createPrivileged', Prenotazione::class);
        $this->reset(['dataPrenotazione', 'idTurno', 'idUtente', 'modificaPrenotazioneId']);
        $this->resetValidation();
        $this->stato = '';
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
        $this->resetValidation();
        $this->modaleAperto = true;
        $this->dispatch('modal-aperto');
    }

    public function chiudiModale()
    {
        $this->modaleAperto = false;
        $this->modalePrivilegiataAperto = false;
        $this->reset(['dataPrenotazione', 'idTurno', 'idUtente', 'modificaPrenotazioneId']);
        $this->resetValidation();
    }

    public function salva()
    {
        $this->validate([
            'dataPrenotazione' => 'required|date',
            'idTurno' => 'required|exists:Turno,id_turno',
            'idUtente' => 'required|exists:Utente,id_utente',
            'stato' => 'required|in:non_confermata,confermata,riservata',
        ]);

        $destinatario = Utente::with('amministratore')->findOrFail($this->idUtente);

        if ($this->stato === 'riservata' && !$destinatario->isDirettivo()) {
            $this->dispatch('toast', tipo: 'error', messaggio: 'Una prenotazione riservata può essere assegnata solo a un membro del direttivo.');
            return;
        }

        if ($this->stato !== 'riservata' && $destinatario->isDirettivo()) {
            $this->dispatch('toast', tipo: 'error', messaggio: 'Ai membri del direttivo si possono assegnare solo prenotazioni riservate.');
            return;
        }

        try {
            $sostituita = null;

            DB::transaction(function () use (&$sostituita) {
                // prenotazione già presente su questo turno e data (esclusa quella che sto modificando)
                $esistente = Prenotazione::with('utente')
                    ->where('id_turno', $this->idTurno)
                    ->whereDate('data_prenotazione', $this->dataPrenotazione)
                    ->when($this->modificaPrenotazioneId, fn($q) => $q->where('id_prenotazione', '!=', $this->modificaPrenotazioneId))
                    ->first();

                if ($esistente) {
                    $this->authorize('delete', $esistente);
                    $sostituita = $esistente->utente->cognome . ' ' . $esistente->utente->nome;
                    $esistente->delete();
                }

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
                }
            });

            $messaggio = $this->modificaPrenotazioneId ? 'Prenotazione aggiornata.' : 'Prenotazione creata.';
            if ($sostituita) {
                $messaggio .= " Sostituita la prenotazione di {$sostituita}.";
            }

            $this->dispatch('toast', tipo: 'success', messaggio: $messaggio);
            $this->chiudiModale();
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('salva prenotazione admin', ['errore' => $e->getMessage()]);
            $this->dispatch('toast', tipo: 'error', messaggio: 'Impossibile salvare la prenotazione.');
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

    private function idUtenteDirettivo(): ?int
    {
        return Utente::where('email', 'aulettaldr@gmail.com')->value('id_utente');
    }

    public function updatedStato()
    {
        if (!$this->idUtente) {
            return;
        }

        $direttivo = Utente::find($this->idUtente)?->isDirettivo();

        if (($this->stato === 'riservata') !== (bool) $direttivo) {
            $this->idUtente = null;
        }
    }
    public function updatedIdUtente()
    {
        if (!$this->idUtente) {
            return;
        }

        $utente = Utente::find($this->idUtente);

        if ($utente?->isDirettivo()) {
            $this->stato = 'riservata';
        } elseif ($this->stato === 'riservata') {
            $this->stato = '';
        }
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
        $utenti = Utente::with('amministratore')->orderBy('cognome')->get()
            ->filter(fn($u) => $this->stato === '' || ($this->stato === 'riservata') === $u->isDirettivo())
            ->values();

        $utenteScelto = $this->idUtente ? Utente::find($this->idUtente) : null;

        $statiAmmessi = $utenteScelto
            ? ($utenteScelto->isDirettivo() ? ['riservata'] : ['non_confermata', 'confermata'])
            : ['non_confermata', 'confermata', 'riservata'];

        $turniOccupati = [];

        if ($this->dataPrenotazione) {
            $turniOccupati = Prenotazione::with('utente')
                ->whereDate('data_prenotazione', $this->dataPrenotazione)
                ->when($this->modificaPrenotazioneId, fn($q) => $q->where('id_prenotazione', '!=', $this->modificaPrenotazioneId))
                ->get()
                ->mapWithKeys(fn($p) => [$p->id_turno => $p->utente->cognome . ' ' . $p->utente->nome])
                ->all();
        }

        return view('livewire.admin.gestione-prenotazioni', [
            'prenotazioni' => $prenotazioni,
            'turni' => $turni,
            'utenti' => $utenti,
            'turniOccupati' => $turniOccupati,
            'statiAmmessi' => $statiAmmessi,
        ]);
    }
}
