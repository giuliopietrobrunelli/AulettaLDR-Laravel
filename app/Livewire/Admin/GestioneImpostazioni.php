<?php

namespace App\Livewire\Admin;

use App\Models\Impostazioni;
use Livewire\Component;

class GestioneImpostazioni extends Component
{
    public string $limiteSettimanale = '';
    public string $settimaneAnticipo = '';

    public function mount()
    {
        $this->limiteSettimanale = Impostazioni::where('nome', 'limite_settimanale')->value('valore') ?? '';
        $this->settimaneAnticipo = Impostazioni::where('nome', 'settimane_anticipo')->value('valore') ?? '';
    }

    public function salva()
    {
        $this->validate([
            'limiteSettimanale' => 'required|integer|min:1',
            'settimaneAnticipo' => 'required|integer|min:0',
        ]);

        $impostazioneLimite = Impostazioni::where('nome', 'limite_settimanale')->first();
        $this->authorize('update', $impostazioneLimite);
        $impostazioneLimite->update(['valore' => $this->limiteSettimanale]);

        $impostazioneAnticipo = Impostazioni::where('nome', 'settimane_anticipo')->first();
        $this->authorize('update', $impostazioneAnticipo);
        $impostazioneAnticipo->update(['valore' => $this->settimaneAnticipo]);

        $this->dispatch('toast', tipo: 'success', messaggio: 'Impostazioni salvate.');
    }

    public function render()
    {
        return view('livewire.admin.gestione-impostazioni');
    }
}