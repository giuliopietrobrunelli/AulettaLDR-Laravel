<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Amministratore', function (Blueprint $table) {
            $table->increments('id_amministratore');
            $table->enum('ruoli_amministratore', ['sviluppatore', 'membro_direttivo']);

            $table->unsignedInteger('id_utente');
            $table->foreign('id_utente')->references('id_utente')->on('Utente')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Amministratore');
    }
};
