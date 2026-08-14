<?php

namespace Database\Seeders;

use App\Models\Impostazioni;
use Illuminate\Database\Seeder;

class ImpostazioniSeeder extends Seeder
{
    public function run(): void
    {
        Impostazioni::updateOrCreate(['nome' => 'limite_settimanale'], ['valore' => '7']);
        Impostazioni::updateOrCreate(['nome' => 'settimane_anticipo'], ['valore' => '1']);
    }
}