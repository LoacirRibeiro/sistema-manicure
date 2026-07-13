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
            $table->foreignId('servico_id')->constrained('servicos')->onDelete('cascade');
            $table->foreignId('manicure_id')->constrained('users')->onDelete('cascade'); // 🔥 Adicionado link com a manicure (users)
            $table->date('data_escolhida'); // 📅 Apenas a data (Y-m-d)
            $table->time('hora_escolhida'); // ⏰ Apenas a hora (H:i)
            $table->string('status')->default('confirmado');
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
