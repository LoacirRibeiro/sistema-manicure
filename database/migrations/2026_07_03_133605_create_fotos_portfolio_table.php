<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotos_portfolio', function (Blueprint $table) {
            $table->id();
            $table->string('caminho_foto'); // Armazena o path gerado pelo Storage
            $table->string('titulo')->nullable(); // Ex: "Alongamento em Fibra de Vidro"
            $table->text('legenda')->nullable(); // Ex: "Formato Amendoador com esmaltação em gel"
            
            // Relacionamento opcional com a profissional que fez o trabalho
            $table->unsignedBigInteger('manicure_id')->nullable();
            $table->foreign('manicure_id')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos_portfolio');
    }
};