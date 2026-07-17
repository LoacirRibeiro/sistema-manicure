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
            Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_nome');
            $table->string('cliente_whatsapp')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('servico_id')->constrained('servicos')->onDelete('cascade');
            $table->foreignId('manicure_id')->constrained('users')->onDelete('cascade'); // 🔥 Adicionado link com a manicure (users)
            $table->date('data_escolhida'); 
            $table->time('hora_escolhida'); 
            $table->string('status')->default('confirmado');
            $table->string('forma_pagamento')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
