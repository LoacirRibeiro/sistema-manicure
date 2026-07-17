<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servico;

class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        $servicos = [
            [
                'nome' => 'Alongamento',
                'descricao' => 'Extensão prática e duradoura utilizando tips de alta qualidade para o comprimento dos sonhos.',               
                'preco' => 150.00,
                'duracao_minutos' => 90,
                'foto_exemplo' => 'unhas1.jpg', 
            ],
            [
                'nome' => 'Banho de Gel',
                'descricao' => 'Técnica altamente resistente e com aspecto super natural. Inclui cutilagem e esmaltação simples.',
                'preco' => 110.00,
                'duracao_minutos' => 120,
                'foto_exemplo' => 'unhas2.jpg', 
            ],
            [
                'nome' => 'Manutenção ',
                'descricao' => 'Reposição do gel, nivelamento e estrutura do alongamento. Recomendado a cada 20 ou 30 dias.',
                'preco' => 120.00,
                'duracao_minutos' => 90,
                'foto_exemplo' => 'unhas3.jpg', 
            ],
            // [
            //     'nome' => 'Blindagem Diamante',
            //     'descricao' => 'Camada de gel sobre a unha natural para evitar quebras e fazer o esmalte durar semanas.',
            //     'preco' => 70.00,
            //     'duracao_minutos' => 60,
            //     'foto_exemplo' => 'unhas4.jpg', 
            // ],
        ];

        foreach ($servicos as $servico) {
            // Usamos updateOrCreate para garantir que o Laravel encontre o serviço e atualize a foto_exemplo correspondente
            Servico::updateOrCreate(
                ['nome' => $servico['nome']], // Chave de busca única
                [
                    'descricao' => $servico['descricao'],       // Sem acento na chave
                    'preco' => $servico['preco'],
                    'duracao_minutos' => $servico['duracao_minutos'], // Sem acento na chave
                    'foto_exemplo' => $servico['foto_exemplo'],
                ]
            );
        }
    }
}