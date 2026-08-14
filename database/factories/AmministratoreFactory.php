<?php

namespace Database\Factories;

use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;

class AmministratoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_utente' => Utente::inRandomOrder()->value('id_utente'),
            'ruoli_amministratore' => $this->faker->randomElement(['sviluppatore', 'membro_direttivo']),
        ];
    }
}