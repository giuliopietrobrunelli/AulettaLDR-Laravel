<?php

namespace App\Livewire;

use App\Models\Feedback;
use Livewire\Component;

class ModalFeedback extends Component
{
    public bool $aperto = false;
    public string $categoria = '';
    public string $contenuto = '';

    protected $listeners = ['apriModalFeedback' => 'apri'];

    public function apri()
    {
        $this->reset(['categoria', 'contenuto']);
        $this->aperto = true;
    }

    public function chiudi()
    {
        $this->aperto = false;
        $this->reset(['categoria', 'contenuto']);
    }

    public function invia()
    {
        $this->authorize('create', Feedback::class);

        $this->validate([
            'categoria' => 'required|in:bug,suggerimento,altro',
            'contenuto' => 'required|string|min:5|max:1000',
        ]);

        Feedback::create([
            'id_utente' => auth()->user()->utente->id_utente,
            'categoria' => $this->categoria,
            'contenuto' => $this->contenuto,
            'stato' => 'non_gestito',
        ]);

        $this->chiudi();
        $this->dispatch('toast', tipo: 'success', messaggio: 'Feedback inviato, grazie!');
    }

    public function render()
    {
        return view('livewire.modal-feedback');
    }
}