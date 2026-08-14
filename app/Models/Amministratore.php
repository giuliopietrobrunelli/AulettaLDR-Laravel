<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amministratore extends Model
{
    use HasFactory;

    protected $table = 'Amministratore';
    protected $primaryKey = 'id_amministratore';
    public $timestamps = false;

    protected $fillable = ['id_amministratore', 'ruoli_amministratore', 'id_utente'];

    public function utente()
    {
        return $this->belongsTo(Utente::class, 'id_utente', 'id_utente');
    }
}