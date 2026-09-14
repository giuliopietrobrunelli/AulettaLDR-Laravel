<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected static function booted(): void
    {
        // Se non viene passato un indice esplicito assegna il prossimo libero
        static::creating(function (Turno $turno) {
            if ($turno->indice === null) {
                $turno->indice = (self::max('indice') ?? 0) + 1;
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
}