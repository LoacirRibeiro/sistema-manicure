<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa a tabela para não duplicar se rodar de novo
        DB::table('horarios')->truncate();

        // 1. Grade de Segunda a Sexta
        $horariosSemana = [
            ['hora' => '09:00', 'dia_tipo' => 'semana'],
            ['hora' => '11:00', 'dia_tipo' => 'semana'],
            ['hora' => '13:00', 'dia_tipo' => 'semana'],
            ['hora' => '15:00', 'dia_tipo' => 'semana'],
            ['hora' => '17:00', 'dia_tipo' => 'semana'],
        ];

        // 2. Grade de Sábado (Conforme solicitado)
        $horariosSabado = [
            ['hora' => '08:00', 'dia_tipo' => 'sabado'],
            ['hora' => '10:00', 'dia_tipo' => 'sabado'],
            ['hora' => '12:00', 'dia_tipo' => 'sabado'],
            ['hora' => '14:00', 'dia_tipo' => 'sabado'],
            ['hora' => '16:00', 'dia_tipo' => 'sabado'],
        ];

        // Insere ambos os blocos no banco
        DB::table('horarios')->insert(array_merge($horariosSemana, $horariosSabado));
    }
}