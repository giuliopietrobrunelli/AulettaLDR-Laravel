<?php

namespace Database\Seeders;

use App\Models\Amministratore;
use App\Models\Utente;
use Illuminate\Database\Seeder;

class AmministratoreSeeder extends Seeder
{
    public function run(): void
    {
        // cerca gli utenti da promuovere per numero_tessera (o email)
        $daPromuovere = [
            ['numero_tessera' => '1', 'ruolo' => 'sviluppatore'],
            ['numero_tessera' => '2', 'ruolo' => 'sviluppatore'],
            ['numero_tessera' => '3', 'ruolo' => 'sviluppatore'],
        ];

        foreach ($daPromuovere as $item) {
            $utente = Utente::where('numero_tessera', $item['numero_tessera'])->first();

            if (!$utente) {
                $this->command->error("Nessun utente trovato con numero tessera {$item['numero_tessera']} — salto.");
                continue;
            }

            Amministratore::updateOrCreate(
                ['id_utente' => $utente->id_utente],
                ['ruoli_amministratore' => $item['ruolo']]
            );
        }
    }
}