<?php

namespace App\Livewire\Admin;

use App\Models\Turno;
use Livewire\Component;

class GestioneTurni extends Component
{
    public bool $modaleApertoCreazione = false;
    public string $orarioInizio = '';
    public string $orarioFine = '';
    public ?int $modificaTurnoId = null;

    public function apriCreazione()
    {
        $this->authorize('create', Turno::class);
        $this->reset(['orarioInizio', 'orarioFine', 'modificaTurnoId']);
        $this->modaleApertoCreazione = true;
    }

    public function apriModifica(int $idTurno)
    {
        $turno = Turno::findOrFail($idTurno);
        $this->authorize('update', $turno);

        $this->modificaTurnoId = $turno->id_turno;
        $this->orarioInizio = substr($turno->orario_inizio, 0, 5);
        $this->orarioFine = substr($turno->orario_fine, 0, 5);
        $this->modaleApertoCreazione = true;
    }

    public function chiudiModale()
    {
        $this->modaleApertoCreazione = false;
        $this->reset(['orarioInizio', 'orarioFine', 'modificaTurnoId']);
    }

    public function salva()
    {
        $this->validate([
            'orarioInizio' => 'required|date_format:H:i',
            'orarioFine' => 'required|date_format:H:i|after:orarioInizio',
        ]);

        if (Turno::sovrappostoConEsistenti($this->orarioInizio, $this->orarioFine, $this->modificaTurnoId)) {
            $this->dispatch('toast', tipo: 'error', messaggio: 'Il turno si sovrappone a uno esistente.');
            return;
        }

        if ($this->modificaTurnoId) {
            $turno = Turno::findOrFail($this->modificaTurnoId);
            $this->authorize('update', $turno);

            $turno->update([
                'orario_inizio' => $this->orarioInizio,
                'orario_fine' => $this->orarioFine,
            ]);

            $this->dispatch('toast', tipo: 'success', messaggio: 'Turno aggiornato.');
        } else {
            $this->authorize('create', Turno::class);

            $maxIndice = Turno::max('indice') ?? 0;

            Turno::create([
                'orario_inizio' => $this->orarioInizio,
                'orario_fine' => $this->orarioFine,
                'indice' => $maxIndice + 1,
                'attivo' => true,
            ]);

            $this->dispatch('toast', tipo: 'success', messaggio: 'Turno creato.');
        }

        $this->chiudiModale();
    }

    public function toggleAttivo(int $idTurno)
    {
        $turno = Turno::findOrFail($idTurno);
        $this->authorize('deactivate', $turno);

        $turno->update(['attivo' => !$turno->attivo]);

        $this->dispatch('toast', tipo: 'success', messaggio: $turno->attivo ? 'Turno riattivato.' : 'Turno disattivato.');
    }

    public function elimina(int $idTurno)
    {
        $turno = Turno::findOrFail($idTurno);
        $this->authorize('delete', $turno);

        $turno->delete();
        $this->dispatch('toast', tipo: 'success', messaggio: 'Turno eliminato (con le sue prenotazioni).');
    }

    public function render()
    {
        $turni = Turno::orderBy('orario_inizio')->get();

        return view('livewire.admin.gestione-turni', [
            'turni' => $turni,
        ]);
    }
}