<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Feedback', function (Blueprint $table) {
            $table->increments('id_feedback');

            $table->unsignedInteger('id_utente');
            $table->foreign('id_utente')->references('id_utente')->on('Utente');

            $table->enum('categoria', ['bug', 'suggerimento', 'domanda', 'altro']);
            $table->text('contenuto');
            $table->timestamp('created_at')->useCurrent();
            $table->enum('stato', ['non_gestito', 'gestito', 'in_lavorazione'])->default('non_gestito');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Feedback');
    }
};
