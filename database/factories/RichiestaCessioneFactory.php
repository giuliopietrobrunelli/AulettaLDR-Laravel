<?php

namespace Database\Factories;

use App\Models\Prenotazione;
use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;

class RichiestaCessioneFactory extends Factory
{
    public function definition(): array
    {
        $tentativi = 0;
        $destinatario = null;
        $prenotazione = null;
        $mittente = null;

        // riprova con una prenotazione diversa se non trova un destinatario idoneo
        while ($destinatario === null && $tentativi < 10) {
            $prenotazione = Prenotazione::whereIn('stato', ['confermata', 'riservata', 'non_confermata'])
                ->inRandomOrder()
                ->first();

            $mittente = $prenotazione->id_utente;

            $destinatario = Utente::where('cauzione', true)
                ->where('registrato', true)
                ->where('id_utente', '!=', $mittente)
                ->inRandomOrder()
                ->value('id_utente');

            $tentativi++;
        }

        return [
            'id_prenotazione' => $prenotazione->id_prenotazione,
            'id_mittente' => $mittente,
            'id_destinatario' => $destinatario,
            'stato' => $this->faker->randomElement(['in_attesa', 'accettata', 'rifiutata', 'scaduta']),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\RichiestaCessione $richiesta) {
            if ($richiesta->stato === 'accettata') {
                Prenotazione::where('id_prenotazione', $richiesta->id_prenotazione)
                    ->update(['id_utente' => $richiesta->id_destinatario]);
            }
        });
    }
}