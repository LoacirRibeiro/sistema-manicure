<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('agendamentos', function (Blueprint $table) {
        $table->integer('qtd_remarcacoes')->default(0)->after('status');
        $table->date('data_original')->nullable()->after('qtd_remarcacoes');
        $table->time('hora_original')->nullable()->after('data_original');
    });
}

public function down()
{
    Schema::table('agendamentos', function (Blueprint $table) {
        $table->dropColumn(['qtd_remarcacoes', 'data_original', 'hora_original']);
    });
}
};
