<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agendamento;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class EnviarLembreteAgendamento extends Command
{
    protected $signature = 'whatsapp:lembrete-agendamentos';
    protected $description = 'Envia lembrete de WhatsApp para clientes com agendamento no dia seguinte';

    public function handle()
    {
        // Busca a data de amanhã
        $amanha = Carbon::tomorrow()->format('Y-m-d');

        // Busca os agendamentos ativos para amanhã
        $agendamentos = Agendamento::with(['servico', 'manicure'])
            ->whereDate('data_escolhida', $amanha)
            ->whereIn('status', ['agendado', 'remarcado'])
            ->get();

        foreach ($agendamentos as $agendamento) {
            if (!empty($agendamento->cliente_whatsapp) && $agendamento->cliente_whatsapp !== 'Não informado') {
                $horaFmt = Carbon::parse($agendamento->hora_escolhida)->format('H:i');
                $servicoNome = $agendamento->servico->nome ?? 'Atendimento';
                $manicureNome = $agendamento->manicure->name ?? 'Profissional';

                WhatsAppService::enviarLembreteAgendamento(
                    $agendamento->cliente_whatsapp,
                    $agendamento->cliente_nome,
                    $servicoNome,
                    $horaFmt,
                    $manicureNome
                );

                $this->info("Lembrete enviado para: {$agendamento->cliente_nome}");
            }
        }

        return Command::SUCCESS;
    }
}