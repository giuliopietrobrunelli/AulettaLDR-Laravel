<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impostazione extends Model
{
    protected $table = 'Impostazioni';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'valore',
    ];

    // uso: Impostazione::get('limite_settimanale', 2)
    public static function get(string $nome, $default = null)
    {
        $valore = static::where('nome', $nome)->value('valore');
        return $valore !== null ? $valore : $default;
    }

    // uso: Impostazione::set('limite_settimanale', 3)
    public static function set(string $nome, $valore): void
    {
        static::updateOrCreate(['nome' => $nome], ['valore' => (string) $valore]);
    }
}
