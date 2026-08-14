<?php

namespace Database\Factories;

use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;

class UtenteFactory extends Factory
{
    protected static ?int $numeroTessera = null;

    public function definition(): array
    {
        // al primo utilizzo, calcola da dove ripartire guardando il DB
        if (self::$numeroTessera === null) {
            self::$numeroTessera = (Utente::max('numero_tessera') ?? 9999) + 1;
        }

        $nome = $this->faker->firstName();
        $cognome = $this->faker->lastName();

        return [
            'numero_tessera' => self::$numeroTessera++,
            'nome' => $nome,
            'cognome' => $cognome,
            'email' => strtolower($nome . '.' . $cognome) . '@studenti.unibs.it',
            'telefono' => $this->faker->numerify('3#########'),
            'facolta_universitaria' => $this->faker->randomElement([
                'Ingegneria Informatica', 'Ingegneria Meccanica', 'Medicina',
                'Economia', 'Giurisprudenza', 'Ingegneria Civile',
            ]),
            'cauzione' => $this->faker->boolean(95),
            'trattamento_dati' => true,
            'registrato' => $this->faker->boolean(90),
            'foto_profilo' => null,
            'vista_predefinita' => $this->faker->randomElement(['month', 'list']),
        ];
    }
}