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
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->string('descricao'); // Ex: Conta de Luz, Aluguel, Esmaltes
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->string('categoria'); // Ex: Salão, Utilidades (Água/Luz), Produtos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despesas');
    }
};
