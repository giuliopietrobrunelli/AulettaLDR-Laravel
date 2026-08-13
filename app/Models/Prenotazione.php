<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prenotazione extends Model
{
    protected $table = 'Prenotazione';
    protected $primaryKey = 'id_prenotazione';
    public $timestamps = false;
    const CREATED_AT = 'data_creazione_prenotazione';

    protected $fillable = [
        'id_utente',
        'id_turno',
        'data_prenotazione',
        'stato',
        'data_conferma',
    ];

    protected $casts = [
        'data_prenotazione' => 'date',
        'data_conferma' => 'datetime',
        'data_creazione_prenotazione' => 'datetime',
    ];

    public function utente()
    {
        return $this->belongsTo(Utente::class, 'id_utente');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'id_turno');
    }

    public function richiesteCessione()
    {
        return $this->hasMany(RichiestaCessione::class, 'id_prenotazione');
    }
}
