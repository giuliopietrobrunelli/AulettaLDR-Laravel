<?php

namespace Database\Seeders;

use App\Models\Utente;
use Illuminate\Database\Seeder;

class UtenteSeeder extends Seeder
{
    public function run(): void
    {
        $utentiFissi = [
            [
            'nome' => 'Ahmed',
            'cognome' => 'Qoqaiche',
            'email' => 'ahmedqoqaiche2005@gmail.com',
            'numero_tessera' => '1',
            'telefono' => '3515138800',
            'facolta_universitaria' => 'Ingegneria Informatica',
            //'ruolo' => 'sviluppatore',
        ],
        [
            'nome' => 'Giulio',
            'cognome' => 'Brunelli',
            'email' => 'brunelligiuliopietro@gmail.com',
            'numero_tessera' => '2',
            'telefono' => '3282305537',
            'facolta_universitaria' => 'Ingegneria Informatica',
            //'ruolo' => 'sviluppatore',
        ],
        [
            'nome' => 'Nicolò',
            'cognome' => 'Cinelli',
            'email' => 'nicocinelli47@gmail.com',
            'numero_tessera' => '3',
            'telefono' => '3394928996',
            'facolta_universitaria' => 'Ingegneria Informatica',
            //'ruolo' => 'sviluppatore',
        ],
        ];

        foreach ($utentiFissi as $utente) {
            $esiste = Utente::where('numero_tessera', $utente['numero_tessera'])
                ->orWhere('email', $utente['email'])
                ->exists();

            if ($esiste) {
                $this->command->error(
                    "Utente con numero tessera {$utente['numero_tessera']} o email {$utente['email']} già esistente — salto."
                );
                continue;
            }

            Utente::create([
                'nome' => $utente['nome'],
                'cognome' => $utente['cognome'],
                'email' => $utente['email'],
                'numero_tessera' => $utente['numero_tessera'],
                'telefono' => $utente['telefono'],
                'facolta_universitaria' => $utente['facolta_universitaria'],
                'cauzione' => true,
                'trattamento_dati' => true,
                'registrato' => true,
                'vista_predefinita' => 'month',
            ]);
        }

        // utenti random, generati DOPO quelli fissi così la Factory
        // legge già il numero_tessera massimo corretto e riparte da lì
        Utente::factory()->count(17)->create();
    }
}