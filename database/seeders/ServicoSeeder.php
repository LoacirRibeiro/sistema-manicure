<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('servicos')->insert([
            [
                'nome' => 'Alongamento em Fibra de Vidro',
                'descricao' => 'Técnica altamente resistente e com aspecto super natural. Inclui cutilagem e esmaltação simples.',
                'preco' => 150.00,
                'duracao_minutos' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Alongamento em Gel na Tips',
                'descricao' => 'Extensão prática e duradoura utilizando tips de alta qualidade para o comprimento dos sonhos.',
                'preco' => 120.00,
                'duracao_minutos' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Manutenção de Alongamento',
                'descricao' => 'Reposição do gel, nivelamento e estrutura do alongamento. Recomendado a cada 20 ou 30 dias.',
                'preco' => 80.00,
                'duracao_minutos' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Blindagem Diamante',
                'descricao' => 'Camada de gel sobre a unha natural para evitar quebras e fazer o esmalte durar semanas.',
                'preco' => 70.00,
                'duracao_minutos' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}