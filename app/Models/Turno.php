<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'Turno';
    protected $primaryKey = 'id_turno';
    public $timestamps = false; // il dump non ha created_at/updated_at su questa tabella

    protected $fillable = [
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
        // replica il comportamento che aveva Supabase: se non viene
        // passato un indice esplicito, assegna il prossimo libero
        static::creating(function (Turno $turno) {
            if ($turno->indice === null) {
                $turno->indice = (self::max('indice') ?? 0) + 1;
            }
        });
    }

    public function prenotazioni()
    {
        return $this->hasMany(Prenotazione::class, 'id_turno');
    }
}
