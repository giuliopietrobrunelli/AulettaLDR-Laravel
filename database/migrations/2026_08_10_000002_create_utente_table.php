<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Utente', function (Blueprint $table) {
            $table->increments('id_utente');

            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('numero_tessera')->unique();
            $table->string('nome');
            $table->string('cognome');
            $table->string('email')->unique();
            $table->string('telefono', 10)->nullable();
            $table->string('facolta_universitaria')->nullable();
            $table->boolean('cauzione')->default(false);
            $table->boolean('trattamento_dati')->default(false);
            $table->boolean('registrato')->default(false);
            $table->string('foto_profilo')->nullable();


            $table->string('vista_predefinita', 50)->default('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Utente');
    }
};
