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
        Schema::table('horarios', function (Blueprint $table) {
            // 'semana' para seg a sex, 'sabado' para os sábados
            $table->string('dia_tipo')->default('semana')->after('hora'); 
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn('dia_tipo');
        });
    }
};
