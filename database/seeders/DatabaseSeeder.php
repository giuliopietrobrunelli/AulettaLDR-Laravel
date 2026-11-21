<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\RichiestaCessione;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TurnoSeeder::class,
            ImpostazioniSeeder::class,
            UtenteSeeder::class,
            AmministratoreSeeder::class,
        ]);

    }

    
}