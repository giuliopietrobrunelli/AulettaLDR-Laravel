<?php

namespace Database\Seeders;

use App\Models\Amministratore;
use App\Models\Feedback;
use App\Models\Impostazioni;
use App\Models\Prenotazione;
use App\Models\RichiestaCessione;
use App\Models\Turno;
use App\Models\Utente;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TurnoSeeder::class,
            ImpostazioniSeeder::class,
            UtenteSeeder::class,
            AmministratoreSeeder::class,
        ]);

        $this->seedPrenotazioni();

        RichiestaCessione::factory()->count(5)->create();
        Feedback::factory()->count(5)->create();
    }

    private function seedPrenotazioni(): void
    {
        $turni = Turno::all(['id_turno', 'orario_inizio']);

        $limiteSettimanale = (int) Impostazioni::where('nome', 'limite_settimanale')->value('valore');
        $settimaneAnticipo = (int) Impostazioni::where('nome', 'settimane_anticipo')->value('valore');

        $adminIds = Amministratore::pluck('id_utente')->all();
        $utentiIdoneiIds = Utente::where('cauzione', true)
            ->where('registrato', true)
            ->pluck('id_utente')
            ->all();

        $dates = [];
        $start = Carbon::now()->subMonth();
        $end = Carbon::now()->addMonth();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        $combinazioni = [];
        foreach ($turni as $turno) {
            foreach ($dates as $data) {
                $combinazioni[] = ['turno' => $turno, 'data_prenotazione' => $data];
            }
        }

        shuffle($combinazioni);
        $daCreare = array_slice($combinazioni, 0, 150);

        $conteggioSettimanale = [];
        $dueMesiFa = Carbon::now()->subMonths(2);

        foreach ($daCreare as $combo) {
            $turno = $combo['turno'];
            $dataPrenotazione = Carbon::parse($combo['data_prenotazione']);

            $inizioTurno = $dataPrenotazione->copy()->setTimeFromTimeString($turno->orario_inizio);
            $fineFinestraConferma = $inizioTurno->copy()->addMinutes(30);

            $finestraConfermaIniziata = $inizioTurno->isPast();
            $finestraConfermaChiusa = $fineFinestraConferma->isPast();

            $settimanaKey = $dataPrenotazione->format('o-W');

            $isRiservata = !$finestraConfermaIniziata && count($adminIds) > 0 && fake()->boolean(10);

            if ($isRiservata) {
                $idUtente = fake()->randomElement($adminIds);
                $stato = 'riservata';
            } else {
                $candidati = array_values(array_filter($utentiIdoneiIds, function ($id) use (&$conteggioSettimanale, $settimanaKey, $limiteSettimanale) {
                    $key = $id . '-' . $settimanaKey;
                    return ($conteggioSettimanale[$key] ?? 0) < $limiteSettimanale;
                }));

                if (empty($candidati)) {
                    continue;
                }

                $idUtente = fake()->randomElement($candidati);

                $stato = $finestraConfermaChiusa
                ? 'confermata'
                : 'non_confermata';
            }

            $key = $idUtente . '-' . $settimanaKey;
            $conteggioSettimanale[$key] = ($conteggioSettimanale[$key] ?? 0) + 1;

            $limiteSuperioreAssoluto = $dataPrenotazione->copy()->subDay()->min(Carbon::now());

            if ($isRiservata) {
                $dataCreazione = fake()->dateTimeBetween($dueMesiFa, $limiteSuperioreAssoluto);
            } else {
                $inizioMeseTurno = $dataPrenotazione->copy()->startOfMonth();
                $finestraA_inizio = $inizioMeseTurno->copy()->max($dueMesiFa);
                $finestraA_fine = $limiteSuperioreAssoluto;
                $finestraA_valida = $finestraA_inizio->lte($finestraA_fine);

                $fineMesePrecedente = $inizioMeseTurno->copy()->subDay();
                $inizioFinestraAnticipo = $inizioMeseTurno->copy()->subWeeks($settimaneAnticipo);
                $finestraB_inizio = $inizioFinestraAnticipo->copy()->max($dueMesiFa);
                $finestraB_fine = $fineMesePrecedente->copy()->min($limiteSuperioreAssoluto);
                $finestraB_valida = $finestraB_inizio->lte($finestraB_fine);

                if (!$finestraA_valida && !$finestraB_valida) {
                    continue;
                }

                if ($finestraA_valida && $finestraB_valida) {
                    [$da, $a] = fake()->boolean()
                        ? [$finestraA_inizio, $finestraA_fine]
                        : [$finestraB_inizio, $finestraB_fine];
                } elseif ($finestraA_valida) {
                    [$da, $a] = [$finestraA_inizio, $finestraA_fine];
                } else {
                    [$da, $a] = [$finestraB_inizio, $finestraB_fine];
                }

                $dataCreazione = fake()->dateTimeBetween($da, $a);
            }

            if ($isRiservata) {
                $dataConferma = $dataCreazione;
            } elseif ($stato === 'confermata') {
                $limiteConfermaSuperiore = $fineFinestraConferma->min(Carbon::now());
                $dataConferma = fake()->dateTimeBetween($inizioTurno, $limiteConfermaSuperiore);
            } else {
                $dataConferma = null;
            }

            Prenotazione::factory()->create([
                'id_turno' => $turno->id_turno,
                'data_prenotazione' => $combo['data_prenotazione'],
                'data_creazione_prenotazione' => $dataCreazione,
                'data_conferma' => $dataConferma,
                'stato' => $stato,
                'id_utente' => $idUtente,
            ]);
        }
    }
}