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
        Schema::create('fluxo_caixas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['Entrada', 'Saída']);
            $table->decimal('valor', 10, 2);
            $table->string('descricao'); // ex: "Agendamento - Alongamento de Gel da Maria" ou "Compra de Gel XED"
            $table->foreignId('agendamento_id')->nullable()->constrained('agendamentos')->onDelete('set null');
            $table->string('forma_pagamento')->nullable(); // Pix, Cartão, Dinheiro
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fluxo_caixas');
    }
};
