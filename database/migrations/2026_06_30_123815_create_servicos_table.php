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
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // ex: Alongamento em Gel, Manutenção, Blindagem
            $table->text('descricao')->nullable(); // Detalhes do que inclui o alongamento
            $table->decimal('preco', 8, 2);
            $table->integer('duracao_minutos')->default(60);
            $table->string('foto_exemplo')->nullable(); // Caminho para uma foto linda do portfólio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
