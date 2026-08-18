<?php

namespace App\Console\Commands;

use App\Models\RichiestaCessione;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ScadiRichiesteCessione extends Command
{
    protected $signature = 'cessioni:scadi';
    protected $description = 'Marca come scadute le richieste di cessione ancora in attesa il cui turno è già iniziato';

    public function handle(): void
    {
        $richieste = RichiestaCessione::where('RichiestaCessione.stato', 'in_attesa')
            ->join('Prenotazione', 'RichiestaCessione.id_prenotazione', '=', 'Prenotazione.id_prenotazione')
            ->join('Turno', 'Prenotazione.id_turno', '=', 'Turno.id_turno')
            ->select('RichiestaCessione.id_richiesta', 'Prenotazione.data_prenotazione', 'Turno.orario_inizio')
            ->get();

        $daScadere = $richieste->filter(function ($r) {
            $inizioTurno = Carbon::parse($r->data_prenotazione . ' ' . $r->orario_inizio);
            return $inizioTurno->isPast();
        });

        $count = $daScadere->count();

        RichiestaCessione::whereIn('id_richiesta', $daScadere->pluck('id_richiesta'))
            ->update(['stato' => 'scaduta']);

        $this->info("Marcate come scadute {$count} richieste di cessione.");
    }
}