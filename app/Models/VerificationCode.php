<?php
// app/Models/VerificationCode.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class VerificationCode extends Model
{
    protected $fillable = ['id_utente', 'code', 'expires_at', 'attempts', 'consumed_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public function utente()
    {
        return $this->belongsTo(Utente::class, 'id_utente', 'id_utente');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return !is_null($this->consumed_at);
    }

    public function attemptsExceeded(): bool
    {
        return $this->attempts >= 5;
    }
}