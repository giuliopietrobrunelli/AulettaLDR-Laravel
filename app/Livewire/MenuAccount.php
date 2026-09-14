<?php

namespace App\Livewire;

use Livewire\Component;

class MenuAccount extends Component
{
    public function render()
    {
        $utente = auth()->user()->utente;
        $isAdmin = $utente && $utente->amministratore !== null;

        return view('livewire.menu-account', [
            'utente' => $utente,
            'isAdmin' => $isAdmin,
        ]);
    }
}