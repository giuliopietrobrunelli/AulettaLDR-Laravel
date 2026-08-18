<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prenotazione extends Model
{
    use HasFactory;

    protected $table = 'Prenotazione';
    protected $primaryKey = 'id_prenotazione';
    public $timestamps = false;
    const created_at = 'data_creazione_prenotazione';

    protected $fillable = [
        'id_prenotazione',
        'data_conferma',
        'id_turno',
        'id_utente',
        'stato',
        'data_prenotazione',
    ];

    protected $casts = [
        'data_creazione_prenotazione' => 'datetime',
        'data_conferma' => 'datetime',
        'data_prenotazione' => 'date',
    ];

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'id_turno', 'id_turno');
    }

    public function utente()
    {
        return $this->belongsTo(Utente::class, 'id_utente', 'id_utente');
    }

    public function richiesteCessione()
    {
        return $this->hasMany(RichiestaCessione::class, 'id_prenotazione', 'id_prenotazione');
    }

    public function scopeVisibili($query)
    {
        return $query->whereHas('turno', function ($q) {
            $q->where('attivo', true);
        });
    }
}