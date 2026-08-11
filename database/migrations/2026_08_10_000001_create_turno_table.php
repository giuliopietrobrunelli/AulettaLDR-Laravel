<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Turno', function (Blueprint $table) {
            $table->increments('id_turno');
            $table->time('orario_inizio');
            $table->time('orario_fine');
            $table->smallInteger('indice')->nullable()->unique();
            $table->boolean('attivo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Turno');
    }
};
