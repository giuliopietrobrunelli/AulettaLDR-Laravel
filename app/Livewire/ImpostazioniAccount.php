<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Component;

class ImpostazioniAccount extends Component
{
    public function render()
    {
        Carbon::setLocale('it');

        $utente = auth()->user()->utente;

        return view('livewire.impostazioni-account', [
            'utente' => $utente,
        ]);
    }
}