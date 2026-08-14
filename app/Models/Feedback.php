<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'Feedback';
    protected $primaryKey = 'id_feedback';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['id_feedback', 'id_utente', 'categoria', 'contenuto', 'stato'];

    public function utente()
    {
        return $this->belongsTo(Utente::class, 'id_utente', 'id_utente');
    }
}