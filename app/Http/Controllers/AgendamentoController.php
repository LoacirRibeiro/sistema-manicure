<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Horario;
use Carbon\Carbon;
use App\Models\Servico;
use App\Models\User; 
use App\Models\Agendamento;
use App\Models\Bloqueio;
use Illuminate\Support\Facades\DB;

class AgendamentoController extends Controller
{
    /**
     * Exibe a tela de seleção de horários para as próximas 2 semanas (Método Principal)
     */
    public function escolherHorario(Request $request)
    {
        // Captura o ID do serviço e manicure vindos do clique no botão ou URL
        $servicoSelecionadoId = $request->input('servico_id');
        $manicureSelecionadaId = $request->input('manicure_id');

        $dataSelecionada = $request->get('data_escolhida', Carbon::today()->format('Y-m-d'));
        $carbonData = Carbon::parse($dataSelecionada);

        $estudioFechado = $carbonData->isSunday();
        $isSabado = $carbonData->isSaturday();

        // --- LÓGICA PARA MAPEAR DIAS LOTADOS/BLOQUEADOS NAS PRÓXIMAS 2 SEMANAS ---
        $diasLotados = [];
        $hoje = Carbon::today();

        for ($i = 0; $i < 14; $i++) {
            $dataVerificar = $hoje->copy()->addDays($i);
            $dataString = $dataVerificar->format('Y-m-d');

            // Ignora domingo, pois já é fechado naturalmente
            if ($dataVerificar->isSunday()) {
                continue;
            }

            // Verifica se o administrador bloqueou o DIA INTEIRO na tabela de bloqueios
            $diaBloqueadoTodo = Bloqueio::where('data', $dataString)
                ->whereNull('horario_inicio')
                ->exists();

            if ($diaBloqueadoTodo) {
                $diasLotados[] = $dataString;
                continue; 
            }

            // Define o limite de vagas baseado no dia da semana
            $limiteHorarios = 5; 

            // Conta quantos agendamentos ativos existem nesse dia
            $qtdAgendados = Agendamento::where('data_escolhida', $dataString)
                ->where('status', '!=', 'cancelado')
                ->count();

            // Se atingiu ou passou o limite, marca o dia como lotado
            if ($qtdAgendados >= $limiteHorarios) {
                $diasLotados[] = $dataString;
            }
        }
        // -------------------------------------------------------------

        // Se o estúdio estiver fechado por ser domingo ou por um bloqueio completo do dia
        $diaBloqueadoTotalmente = Bloqueio::where('data', $dataSelecionada)->whereNull('horario_inicio')->exists();

        if ($estudioFechado || $diaBloqueadoTotalmente) {
            $horariosDisponiveis = collect(); 
            $servicos = Servico::all();
            $manicures = User::all(); 
            $estudioFechado = true; 
        } else {
            // 1. Descobre quais horários já foram agendados para este dia específico, IGNORANDO os cancelados
            $horariosOcupados = Agendamento::where('data_escolhida', $dataSelecionada)
                ->where('status', '!=', 'cancelado')
                ->pluck('hora_escolhida')
                ->map(function($hora) {
                    return \Carbon\Carbon::parse($hora)->format('H:i');
                })
                ->toArray();

            // 2. Define o grid de horários permitidos no dia
            $horasPermitidas = $isSabado 
                ? ['08:00', '10:00', '12:00', '14:00', '16:00']
                : ['09:00', '11:00', '13:00', '15:00', '17:00'];

            // 3. Busca todos os horários que bateriam com o grid padrão
            $todosHorariosDoGrid = Horario::whereIn(DB::raw("DATE_FORMAT(hora, '%H:%i')"), $horasPermitidas)
                ->orderBy('hora')
                ->get();

            // 4. Busca bloqueios parciais de horário para o dia selecionado
            $bloqueiosParciais = Bloqueio::where('data', $dataSelecionada)
                ->whereNotNull('horario_inicio')
                ->whereNotNull('horario_fim')
                ->get();

            // 5. Filtra a lista final removendo agendamentos E aplicando faixas de horários bloqueadas
            $horariosDisponiveis = $todosHorariosDoGrid->filter(function($item) use ($horariosOcupados, $bloqueiosParciais, $dataSelecionada) {
                $horaFormatada = Carbon::parse($item->hora)->format('H:i');

                // Se a data selecionada for HOJE, remove os horários que já passaram do minuto atual
                if ($dataSelecionada === Carbon::today()->format('Y-m-d')) {
                    $agora = Carbon::now('-03:00')->format('H:i'); // Força o fuso horário correto se necessário
                    if ($horaFormatada <= $agora) {
                        return false; // Se o horário já passou (ex: são 09:01 e o horário é 09:00), some da tela
                    }
                }

                // Critério A: Remove se já estiver ocupado por um agendamento de cliente
                if (in_array($horaFormatada, $horariosOcupados)) {
                    return false;
                }

                // Critério B: Remove se estiver dentro de uma faixa de horário bloqueada pelo admin
                foreach ($bloqueiosParciais as $bloqueio) {
                    $inicio = Carbon::parse($bloqueio->horario_inicio)->format('H:i');
                    $fim = Carbon::parse($bloqueio->horario_fim)->format('H:i');

                    // Se a hora do grid cair dentro da janela de bloqueio (inclusive no limite de início)
                    if ($horaFormatada >= $inicio && $horaFormatada < $fim) {
                        return false; 
                    }
                }

                return true; 
            });
                
            $servicos = Servico::all();
            $manicures = User::role('manicure')->get();
        }

        $servicoSelecionado = Servico::find($servicoSelecionadoId);

        return view('agendamento.horarios', compact(
            'dataSelecionada', 
            'estudioFechado', 
            'horariosDisponiveis', 
            'servicos', 
            'manicures',
            'diasLotados',
            'servicoSelecionadoId',
            'servicoSelecionado',
            'manicureSelecionadaId'
        ));
    }

    /**
     * Processa o formulário de agendamento (Ação do Botão Confirmar)
     */
    public function salvarAgendamento(Request $request)
    {
        $dadosValidados = $request->validate([
            'cliente_nome'     => 'required|string|max:255',
            'cliente_whatsapp' => 'nullable|string',
            'servico_id'       => 'required|exists:servicos,id',
            'manicure_id'      => 'required|exists:users,id',
            'data_escolhida'   => 'required|date',
            'hora_escolhida'   => 'required',
        ]);

        // Segunda Camada de Segurança: Evita burlar se alguém inspecionar o HTML
        $horaFormatada = Carbon::parse($dadosValidados['hora_escolhida'])->format('H:i');
        
        $estaBloqueado = Bloqueio::where('data', $dadosValidados['data_escolhida'])
            ->where(function($query) use ($horaFormatada) {
                // Bloqueio do dia inteiro OU horário dentro da faixa bloqueada
                $query->whereNull('horario_inicio')
                      ->orWhere(function($q) use ($horaFormatada) {
                          $q->where('horario_inicio', '<=', $horaFormatada)
                            ->where('horario_fim', '>', $horaFormatada);
                      });
            })->exists();

        if ($estaBloqueado) {
            return redirect()->back()->withErrors(['hora_escolhida' => 'Desculpe, este horário acabou de ser bloqueado administrativamente.']);
        }

        Agendamento::create([
            'cliente_nome'     => $dadosValidados['cliente_nome'],
            'cliente_whatsapp' => $dadosValidados['cliente_whatsapp'] ?? 'Não informado',
            'servico_id'       => $dadosValidados['servico_id'],
            'manicure_id'      => $dadosValidados['manicure_id'],
            'data_escolhida'   => $dadosValidados['data_escolhida'],
            'hora_escolhida'   => $dadosValidados['hora_escolhida'],
            'status'           => 'confirmado',
        ]);

        return redirect()->route('home.index')->with('sucesso', 'Seu agendamento foi realizado com sucesso!');
    }

    public function meusAgendamentos()
    {
        // Busca os agendamentos pelo nome do cliente logado
        $agendamentos = Agendamento::where('cliente_nome', auth()->user()->name)
            ->with(['servico', 'manicure'])
            ->orderBy('data_escolhida', 'desc')
            ->orderBy('hora_escolhida', 'desc')
            ->get();

        // Retorna a view onde o cliente poderá ver o histórico/status dos seus agendamentos
        return view('cliente.agendamentos', compact('agendamentos'));
    }
}