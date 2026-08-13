<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'Feedback';
    protected $primaryKey = 'id_feedback';
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'id_utente',
        'categoria',
        'contenuto',
        'stato',
    ];

    public function utente()
    {
        return $this->belongsTo(Utente::class, 'id_utente');
    }
}
