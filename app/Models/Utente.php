<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utente extends Model
{
    use HasFactory;

    protected $table = 'Utente';
    protected $primaryKey = 'id_utente';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'numero_tessera', 'nome', 'cognome', 'email', 'telefono',
        'facolta_universitaria', 'cauzione', 'trattamento_dati', 'registrato',
        'foto_profilo', 'vista_predefinita',
    ];

    protected $casts = [
        'cauzione' => 'boolean',
        'trattamento_dati' => 'boolean',
        'registrato' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prenotazioni()
    {
        return $this->hasMany(Prenotazione::class, 'id_utente', 'id_utente');
    }

    public function amministratore()
    {
        return $this->hasOne(Amministratore::class, 'id_utente', 'id_utente');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'id_utente', 'id_utente');
    }

    public function verificationCodes()
{
    return $this->hasMany(VerificationCode::class, 'id_utente', 'id_utente');
}
}