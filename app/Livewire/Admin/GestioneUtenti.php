<?php

namespace App\Livewire\Admin;

use App\Models\Utente;
use Livewire\Component;
use Livewire\WithPagination;

class GestioneUtenti extends Component
{
    use WithPagination;

    public bool $modaleAperto = false;
    public ?int $modificaUtenteId = null;

    public string $nome = '';
    public string $cognome = '';
    public string $email = '';
    public string $numeroTessera = '';
    public string $telefono = '';
    public string $facoltaUniversitaria = '';
    public bool $cauzione = false;
    public bool $trattamentoDati = false;
    public string $filtroStato = 'registrati';

    public string $ricerca = '';

    public function elimina()
    {
        $utente = Utente::findOrFail($this->modificaUtenteId);
        $this->authorize('delete', $utente);

        $utente->delete();

        $this->chiudiModale();
        $this->dispatch('toast', tipo: 'success', messaggio: 'Utente eliminato.');
    }

    public function apriCreazione()
    {
        $this->authorize('create', Utente::class);
        $this->reset(['nome', 'cognome', 'email', 'numeroTessera', 'telefono', 'facoltaUniversitaria', 'cauzione', 'trattamentoDati', 'modificaUtenteId']);
        $this->modaleAperto = true;
        $this->dispatch('modal-aperto');
    }

    public function apriModifica(int $idUtente)
    {
        $utente = Utente::findOrFail($idUtente);
        $this->authorize('update', $utente);

        $this->modificaUtenteId = $utente->id_utente;
        $this->nome = $utente->nome;
        $this->cognome = $utente->cognome;
        $this->email = $utente->email;
        $this->numeroTessera = (string) $utente->numero_tessera;
        $this->telefono = $utente->telefono ?? '';
        $this->facoltaUniversitaria = $utente->facolta_universitaria ?? '';
        $this->cauzione = (bool) $utente->cauzione;
        $this->trattamentoDati = (bool) $utente->trattamento_dati;
        $this->modaleAperto = true;
        $this->dispatch('modal-aperto');
    }

    public function chiudiModale()
    {
        $this->modaleAperto = false;
        $this->reset(['nome', 'cognome', 'email', 'numeroTessera', 'telefono', 'facoltaUniversitaria', 'cauzione', 'trattamentoDati', 'modificaUtenteId']);
    }

    public function salva()
    {
        $regolaEmail = 'required|email|max:255|unique:Utente,email' . ($this->modificaUtenteId ? ',' . $this->modificaUtenteId . ',id_utente' : '');
        $regolaTessera = 'required|digits_between:1,10|unique:Utente,numero_tessera' . ($this->modificaUtenteId ? ',' . $this->modificaUtenteId . ',id_utente' : '');

        $this->validate([
            'email' => $regolaEmail,
            'numeroTessera' => $regolaTessera,
            'telefono' => 'nullable|digits:10',
            'facoltaUniversitaria' => 'nullable|string|max:255',
        ]);

        if ($this->modificaUtenteId) {
            $utente = Utente::findOrFail($this->modificaUtenteId);
            $this->authorize('update', $utente);

            $utente->update([
                'email' => $this->email,
                'numero_tessera' => $this->numeroTessera,
                'telefono' => $this->telefono ?: null,
                'facolta_universitaria' => $this->facoltaUniversitaria ?: null,
                'cauzione' => $this->cauzione,
                'trattamento_dati' => $this->trattamentoDati,
            ]);

            $this->dispatch('toast', tipo: 'success', messaggio: 'Utente aggiornato.');
        } else {
            $this->authorize('create', Utente::class);

            $this->validate([
                'nome' => 'required|string|max:255',
                'cognome' => 'required|string|max:255',
            ]);

            Utente::create([
                'nome' => $this->nome,
                'cognome' => $this->cognome,
                'email' => $this->email,
                'numero_tessera' => $this->numeroTessera,
                'telefono' => $this->telefono ?: null,
                'facolta_universitaria' => $this->facoltaUniversitaria ?: null,
                'cauzione' => $this->cauzione,
                'trattamento_dati' => $this->trattamentoDati,
                'registrato' => false,
                'vista_predefinita' => 'month',
            ]);

            $this->dispatch('toast', tipo: 'success', messaggio: 'Utente creato.');
        }

        $this->chiudiModale();
    }

    public function render()
    {
        $utenti = Utente::when($this->filtroStato === 'registrati', fn($q) => $q->where('registrato', true))
            ->when($this->filtroStato === 'non_registrati', fn($q) => $q->where('registrato', false))
            ->when($this->ricerca, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('nome', 'like', '%' . $this->ricerca . '%')
                        ->orWhere('cognome', 'like', '%' . $this->ricerca . '%')
                        ->orWhere('email', 'like', '%' . $this->ricerca . '%')
                        ->orWhere('numero_tessera', 'like', '%' . $this->ricerca . '%');
                });
            })
            ->orderBy('cognome')
            ->paginate(20);

        return view('livewire.admin.gestione-utenti', [
            'utenti' => $utenti,
        ]);
    }
}