<?php

namespace Database\Factories;

use App\Models\Prenotazione;
use App\Models\Turno;
use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RichiestaCessioneFactory extends Factory
{
    public function definition(): array
    {
        $tentativi = 0;
        $destinatario = null;
        $prenotazione = null;
        $mittente = null;

        while ($destinatario === null && $tentativi < 10) {
            $prenotazione = Prenotazione::whereIn('stato', ['confermata', 'non_confermata'])
                ->inRandomOrder()
                ->first();

            if (!$prenotazione) {
                $tentativi++;
                continue;
            }

            $mittente = $prenotazione->id_utente;

            $destinatario = Utente::where('cauzione', true)
                ->where('registrato', true)
                ->where('id_utente', '!=', $mittente)
                ->inRandomOrder()
                ->value('id_utente');

            $tentativi++;
        }

        // calcola created_at coerente: dopo la creazione della prenotazione, prima dell'inizio turno
        $createdAt = now();
        if ($prenotazione) {
            $turno = Turno::find($prenotazione->id_turno);
            $dataPrenotazioneStr = Carbon::parse($prenotazione->data_prenotazione)->format('Y-m-d');
            $inizioTurno = Carbon::parse($dataPrenotazioneStr . ' ' . $turno->orario_inizio);
            $dataCreazionePrenotazione = Carbon::parse($prenotazione->data_creazione_prenotazione);

            $limiteSuperiore = $inizioTurno->min(now());

            if ($dataCreazionePrenotazione->lt($limiteSuperiore)) {
                $createdAt = $this->faker->dateTimeBetween($dataCreazionePrenotazione, $limiteSuperiore);
            } else {
                $createdAt = $dataCreazionePrenotazione;
            }
        }

        return [
            'id_prenotazione' => $prenotazione->id_prenotazione,
            'id_mittente' => $mittente,
            'id_destinatario' => $destinatario,
            'stato' => $this->faker->randomElement(['in_attesa', 'accettata', 'rifiutata', 'scaduta']),
            'created_at' => $createdAt,
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