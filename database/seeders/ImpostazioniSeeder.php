<?php

namespace Database\Seeders;

use App\Models\Impostazione;
use Illuminate\Database\Seeder;

class ImpostazioniSeeder extends Seeder
{
    public function run(): void
    {
        Impostazione::updateOrCreate(['nome' => 'limite_settimanale'], ['valore' => '2']);
        Impostazione::updateOrCreate(['nome' => 'settimane_anticipo'], ['valore' => '1']);
    }
}
