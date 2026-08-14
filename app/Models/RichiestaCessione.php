<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RichiestaCessione extends Model
{
    use HasFactory;

    protected $table = 'RichiestaCessione';
    protected $primaryKey = 'id_richiesta';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_richiesta', 'id_prenotazione', 'id_mittente', 'id_destinatario', 'stato',
    ];

    public function prenotazione()
    {
        return $this->belongsTo(Prenotazione::class, 'id_prenotazione', 'id_prenotazione');
    }

    public function mittente()
    {
        return $this->belongsTo(Utente::class, 'id_mittente', 'id_utente');
    }

    public function destinatario()
    {
        return $this->belongsTo(Utente::class, 'id_destinatario', 'id_utente');
    }
}