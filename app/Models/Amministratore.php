<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amministratore extends Model
{
    protected $table = 'Amministratore';
    protected $primaryKey = 'id_amministratore';
    public $timestamps = false;

    protected $fillable = [
        'ruolo',
        'id_utente',
    ];

    public function utente()
    {
        return $this->belongsTo(Utente::class, 'id_utente');
    }
}
