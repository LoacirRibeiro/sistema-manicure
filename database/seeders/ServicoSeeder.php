<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servico; // Certifique-se de que o Model Servico está na pasta padrão

class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        // Lista de serviços com seus respectivos nomes de arquivos de imagem na pasta public/img/
        $servicos = [
            [
                'nome' => 'Alongamento em Fibra de Vidro',
                'descricao' => 'Técnica altamente resistente e com aspecto super natural. Inclui cutilagem e esmaltação simples.',
                'preco' => 150.00,
                'duracao_minutos' => 150,
                'foto_exemplo' => 'imagem1.webp', // Adicione aqui o nome exato do arquivo que está em public/img/
            ],
            [
                'nome' => 'Alongamento em Gel na Tips',
                'descricao' => 'Extensão prática e duradoura utilizando tips de alta qualidade para o comprimento dos sonhos.',
                'preco' => 120.00,
                'duracao_minutos' => 120,
                'foto_exemplo' => 'imagem2.webp', // Adicione aqui o nome do arquivo da foto do serviço
            ],
            [
                'nome' => 'Manutenção de Alongamento',
                'descricao' => 'Reposição do gel, nivelamento e estrutura do alongamento. Recomendado a cada 20 ou 30 dias.',
                'preco' => 80.00,
                'duracao_minutos' => 90,
                'foto_exemplo' => 'imagem3.webp', // Adicione o nome do arquivo correspondente
            ],
            [
                'nome' => 'Blindagem Diamante',
                'descricao' => 'Camada de gel sobre a unha natural para evitar quebras e fazer o esmalte durar semanas.',
                'preco' => 70.00,
                'duracao_minutos' => 60,
                'foto_exemplo' => 'imagem4.jpg', // Adicione o nome do arquivo correspondente
            ],
        ];

        foreach ($servicos as $servico) {
            // firstOrCreate verifica se o serviço já existe pelo nome antes de criar, evitando duplicar 3x
            Servico::firstOrCreate(
                ['nome' => $servico['nome']], // Chave de busca para verificar existência
                [
                    'descricao' => $servico['descricao'],
                    'preco' => $servico['preco'],
                    'duracao_minutos' => $servico['duracao_minutos'],
                    'foto_exemplo' => $servico['foto_exemplo'],
                ]
            );
        }
    }
}