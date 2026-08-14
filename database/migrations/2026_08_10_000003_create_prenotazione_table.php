<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Prenotazione', function (Blueprint $table) {
            $table->increments('id_prenotazione');
            $table->timestamp('data_creazione_prenotazione')->useCurrent();
            $table->dateTime('data_conferma')->nullable();

            $table->unsignedInteger('id_turno');
            $table->foreign('id_turno')->references('id_turno')->on('Turno')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('id_utente');
            $table->foreign('id_utente')->references('id_utente')->on('Utente')->onUpdate('cascade')->onDelete('cascade');

            $table->enum('stato', ['non_confermata', 'confermata', 'riservata', 'annullata'])->default('non_confermata');
            $table->date('data_prenotazione');

            
            $table->unique(['id_turno', 'data_prenotazione']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Prenotazione');
    }
};
