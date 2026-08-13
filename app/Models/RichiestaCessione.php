<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RichiestaCessione extends Model
{
    protected $table = 'RichiestaCessione';
    protected $primaryKey = 'id_richiesta';
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'id_prenotazione',
        'id_mittente',
        'id_destinatario',
        'stato',
    ];

    public function prenotazione()
    {
        return $this->belongsTo(Prenotazione::class, 'id_prenotazione');
    }

    public function mittente()
    {
        return $this->belongsTo(Utente::class, 'id_mittente');
    }

    public function destinatario()
    {
        return $this->belongsTo(Utente::class, 'id_destinatario');
    }
}
