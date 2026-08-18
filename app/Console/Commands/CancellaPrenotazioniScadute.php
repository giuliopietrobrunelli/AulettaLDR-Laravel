<?php

namespace App\Console\Commands;

use App\Models\Prenotazione;
use App\Models\Turno;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CancellaPrenotazioniScadute extends Command
{
    protected $signature = 'prenotazioni:cancella-scadute';
    protected $description = 'Cancella le prenotazioni non confermate entro 30 minuti dall\'inizio turno';

    public function handle(): void
    {
        $prenotazioni = Prenotazione::where('stato', 'non_confermata')
            ->join('Turno', 'Prenotazione.id_turno', '=', 'Turno.id_turno')
            ->select('Prenotazione.*', 'Turno.orario_inizio')
            ->get();

        $daCancellare = $prenotazioni->filter(function ($p) {
            $dataStr = Carbon::parse($p->data_prenotazione)->format('Y-m-d');
            $inizioTurno = Carbon::parse($dataStr . ' ' . $p->orario_inizio);
            return $inizioTurno->copy()->addMinutes(30)->isPast();
        });

        $count = $daCancellare->count();

        Prenotazione::whereIn('id_prenotazione', $daCancellare->pluck('id_prenotazione'))->delete();

        $this->info("Cancellate {$count} prenotazioni scadute.");
    }
}