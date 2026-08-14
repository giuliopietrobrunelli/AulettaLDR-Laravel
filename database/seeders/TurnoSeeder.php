<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        $turni = [
            ['orario_inizio' => '08:00:00', 'orario_fine' => '10:00:00', 'indice' => 1, 'attivo' => true],
            ['orario_inizio' => '10:00:00', 'orario_fine' => '12:00:00', 'indice' => 2, 'attivo' => true],
            ['orario_inizio' => '12:00:00', 'orario_fine' => '13:30:00', 'indice' => 3, 'attivo' => true],
            ['orario_inizio' => '13:30:00', 'orario_fine' => '15:30:00', 'indice' => 4, 'attivo' => true],
            ['orario_inizio' => '15:30:00', 'orario_fine' => '17:30:00', 'indice' => 5, 'attivo' => true],
            ['orario_inizio' => '17:30:00', 'orario_fine' => '19:30:00', 'indice' => 6, 'attivo' => true],
            ['orario_inizio' => '19:30:00', 'orario_fine' => '21:00:00', 'indice' => 7, 'attivo' => true],
            ['orario_inizio' => '21:00:00', 'orario_fine' => '23:00:00', 'indice' => 8, 'attivo' => true],
        ];

        DB::table('Turno')->insert($turni);
    }
}