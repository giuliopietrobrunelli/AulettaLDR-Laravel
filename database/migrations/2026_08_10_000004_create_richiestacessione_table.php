<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('RichiestaCessione', function (Blueprint $table) {
            $table->increments('id_richiesta');

            $table->unsignedInteger('id_prenotazione');
            $table->foreign('id_prenotazione')->references('id_prenotazione')->on('Prenotazione')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('id_mittente');
            $table->foreign('id_mittente')->references('id_utente')->on('Utente')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('id_destinatario');
            $table->foreign('id_destinatario')->references('id_utente')->on('Utente')->onUpdate('cascade')->onDelete('cascade');

            $table->enum('stato', ['in_attesa', 'accettata', 'rifiutata', 'scaduta'])
                  ->default('in_attesa');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('RichiestaCessione');
    }
};
