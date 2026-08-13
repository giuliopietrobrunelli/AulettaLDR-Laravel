<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Utente extends Model
{
    protected $table = 'Utente';
    protected $primaryKey = 'id_utente';
    public $timestamps = false; // il dump non ha created_at/updated_at su questa tabella

    protected $fillable = [
        'user_id',
        'numero_tessera',
        'nome',
        'cognome',
        'email',
        'telefono',
        'facolta_universitaria',
        'cauzione',
        'trattamento_dati',
        'registrato',
        'foto_profilo',
        'mostra_foto_prenotazioni',
        'vista_predefinita',
    ];

    protected $casts = [
        'cauzione' => 'boolean',
        'trattamento_dati' => 'boolean',
        'registrato' => 'boolean',
        'mostra_foto_prenotazioni' => 'boolean',
    ];

    // ── relazioni ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prenotazioni()
    {
        return $this->hasMany(Prenotazione::class, 'id_utente');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'id_utente');
    }

    public function amministratore()
    {
        return $this->hasOne(Amministratore::class, 'id_utente');
    }

    // richieste di cessione ricevute e ancora da gestire — sostituisce
    // quello che prima arrivava dalla tabella Notifica (rimossa)
    public function richiesteCessioneRicevute()
    {
        return $this->hasMany(RichiestaCessione::class, 'id_destinatario');
    }

    public function richiesteCessioneInviate()
    {
        return $this->hasMany(RichiestaCessione::class, 'id_mittente');
    }

    public function isAmministratore(): bool
    {
        return $this->amministratore()->exists();
    }
}
