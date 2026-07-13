<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bloqueios', function (Blueprint $table) {
            $table->id();
            $table->date('data'); // Dia do bloqueio (ex: 2026-07-10)
            $table->time('horario_inicio')->nullable(); // Se nulo, bloqueia o dia todo
            $table->time('horario_fim')->nullable();
            $table->string('motivo')->nullable(); // ex: Feriado, Curso, Manutenção
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloqueios');
    }
};
