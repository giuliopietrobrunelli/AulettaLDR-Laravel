<?php

namespace Database\Factories;

use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_utente' => Utente::where('registrato', true)
                ->inRandomOrder()
                ->value('id_utente'),
            'categoria' => $this->faker->randomElement(['bug', 'suggerimento', 'domanda', 'altro']),
            'contenuto' => $this->faker->sentence(12),
            'stato' => $this->faker->randomElement(['non_gestito', 'gestito', 'in_lavorazione']),
        ];
    }
}