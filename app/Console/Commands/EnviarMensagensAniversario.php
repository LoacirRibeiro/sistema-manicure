<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class EnviarMensagensAniversario extends Command
{
    protected $signature = 'whatsapp:aniversariantes';
    protected $description = 'Envia mensagem de feliz aniversário via WhatsApp para as clientes do dia';

    public function handle()
    {
        $hoje = Carbon::today();

        // Busca usuários que fazem aniversário hoje (mês e dia)
        $aniversariantes = User::whereMonth('data_nascimento', $hoje->month)
            ->whereDay('data_nascimento', $hoje->day)
            ->get();

        foreach ($aniversariantes as $cliente) {
            $telefone = $cliente->telefone ?? $cliente->whatsapp;

            if ($telefone) {
                WhatsAppService::enviarMensagemAniversario($telefone, $cliente->name);
                $this->info("Mensagem enviada para: {$cliente->name}");
            }
        }

        return Command::SUCCESS;
    }
}