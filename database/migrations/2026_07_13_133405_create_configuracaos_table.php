<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracaos', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('foto_hero')->nullable(); // Caminho da foto inicial
            $blueprint->string('foto_espaco')->nullable(); // Caminho da foto do ambiente
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracaos');
    }
};