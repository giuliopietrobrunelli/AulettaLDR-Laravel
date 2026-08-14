<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impostazioni extends Model
{
    use HasFactory;

    protected $table = 'Impostazioni';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['id', 'nome', 'valore'];

    public static function get(string $nome, $default = null)
    {
        $valore = static::where('nome', $nome)->value('valore');
        return $valore !== null ? $valore : $default;
    }
    public static function set(string $nome, $valore): void 
    {
        static::updateOrCreate(['nome' => $nome], ['valore' => (int)$valore]);
    }
}