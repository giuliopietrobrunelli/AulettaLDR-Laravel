<?php

namespace App\Livewire\Admin;

use App\Services\StatisticheService;
use Livewire\Component;

class Statistiche extends Component
{
    public function mount()
    {
        $utente = auth()->user()->utente;

        if (!$utente || $utente->amministratore === null) {
            abort(403, 'Accesso riservato agli amministratori.');
        }
    }

    public function render(StatisticheService $service)
    {
        $dati = $service->riepilogo();

        return view('livewire.admin.statistiche', [
            'dati' => $dati,
        ]);
    }
}