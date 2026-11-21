<?php

namespace App\Livewire\Admin;

use App\Models\Turno;
use Livewire\Component;
use App\Models\Impostazioni;
use App\Models\Prenotazione;
use Illuminate\Support\Carbon;

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

            Turno::create([
                'orario_inizio' => $this->orarioInizio,
                'orario_fine' => $this->orarioFine,
                'attivo' => true,
            ]);

            $this->dispatch('toast', tipo: 'success', messaggio: 'Turno creato.');
        }

        Turno::riordinaIndici();
        $this->chiudiModale();
    }
    private function utentiOltreLimite(Turno $turno): array
    {
        $limite = (int) Impostazioni::where('nome', 'limite_settimanale')->value('valore');

        $prenotazioni = Prenotazione::with('utente')
            ->where('id_turno', $turno->id_turno)
            ->whereDate('data_prenotazione', '>=', Carbon::today())
            ->get();

        $nomi = [];

        foreach ($prenotazioni as $p) {
            $data = Carbon::parse($p->data_prenotazione);

            $conteggio = Prenotazione::where('id_utente', $p->id_utente)
                ->whereBetween('data_prenotazione', [
                    $data->copy()->startOfWeek(),
                    $data->copy()->endOfWeek(),
                ])
                ->count();

            if ($conteggio > $limite) {
                $nomi[$p->id_utente] = $p->utente->cognome . ' ' . $p->utente->nome;
            }
        }

        return array_values($nomi);
    }

    public function toggleAttivo(int $idTurno)
    {
        $turno = Turno::findOrFail($idTurno);
        $this->authorize('deactivate', $turno);

        $turno->update(['attivo' => !$turno->attivo]);

        if (!$turno->attivo) {
            $this->dispatch('toast', tipo: 'success', messaggio: 'Turno disattivato.');
            return;
        }

        $this->dispatch('toast', tipo: 'success', messaggio: 'Turno riattivato.');

        $oltreLimite = $this->utentiOltreLimite($turno);

        if (count($oltreLimite) > 0) {
            $elenco = implode(', ', array_slice($oltreLimite, 0, 3));
            if (count($oltreLimite) > 3) {
                $elenco .= ' e altri ' . (count($oltreLimite) - 3);
            }

            $this->dispatch(
                'toast',
                tipo: 'info',
                messaggio: "Attenzione: riattivando questo turno {$elenco} supera il limite settimanale di prenotazioni."
            );
        }
    }

    public function elimina(int $idTurno)
    {
        $turno = Turno::findOrFail($idTurno);
        $this->authorize('delete', $turno);

        $turno->delete();
        Turno::riordinaIndici();
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