<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'Turno';
    protected $primaryKey = 'id_turno';
    public $timestamps = false;

    protected $fillable = [
        'id_turno',
        'orario_inizio',
        'orario_fine',
        'indice',
        'attivo',
    ];

    protected $casts = [
        'attivo' => 'boolean',
    ];

    // Riscrive gli indici: 1..n sui turni attivi in ordine di orario di inizio, NULL sui disattivati
    public static function riordinaIndici(): void
    {
        DB::transaction(function () {
            // prima azzera tutto: aggiornare un indice alla volta violerebbe il vincolo unique
            self::query()->update(['indice' => null]);

            $indice = 1;
            foreach (self::where('attivo', true)->orderBy('orario_inizio')->get() as $turno) {
                $turno->update(['indice' => $indice++]);
            }
        });
    }

    public function prenotazioni()
    {
        return $this->hasMany(Prenotazione::class, 'id_turno', 'id_turno');
    }

    public static function sovrappostoConEsistenti(string $orarioInizio, string $orarioFine, ?int $escludiId = null): bool
    {
        $query = self::where('attivo', true)
            ->where(function ($q) use ($orarioInizio, $orarioFine) {
                $q->where('orario_inizio', '<', $orarioFine)
                    ->where('orario_fine', '>', $orarioInizio);
            });

        if ($escludiId !== null) {
            $query->where('id_turno', '!=', $escludiId);
        }

        return $query->exists();
    }

    // true se il turno, nella data indicata, è già finito
    public function terminatoIl(string $data): bool
    {
        return Carbon::parse(Carbon::parse($data)->format('Y-m-d') . ' ' . $this->orario_fine)->isPast();
    }
}
