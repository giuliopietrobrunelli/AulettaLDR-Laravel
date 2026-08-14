<?php

namespace Database\Factories;

use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrenotazioneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'data_creazione_prenotazione' => now(),
            'data_conferma' => null,
            'stato' => 'non_confermata',
            'id_utente' => Utente::where('cauzione', true)
                ->where('registrato', true)
                ->inRandomOrder()
                ->value('id_utente'),
        ];
    }
}