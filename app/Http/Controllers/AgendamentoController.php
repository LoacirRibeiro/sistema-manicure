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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Importação da Facade Hash adicionada de forma limpa

class AgendamentoController extends Controller
{
    /**
     * Exibe a tela de seleção de horários para as próximas 2 semanas (Método Principal)
     */
    public function escolherHorario(Request $request)
    {
        $usuarioLogado = Auth::user();
        $estaDeCastigo = $usuarioLogado && $usuarioLogado->bloqueado_ate && $usuarioLogado->bloqueado_ate->isFuture();

        $servicoSelecionadoId = $request->input('servico_id');
        $manicureSelecionadaId = $request->input('manicure_id');

        $dataSelecionada = $request->get('data_escolhida', Carbon::today()->format('Y-m-d'));
        $carbonData = Carbon::parse($dataSelecionada);

        $estudioFechado = $carbonData->isSunday();
        $isSabado = $carbonData->isSaturday();

        $diasLotados = [];
        $hoje = Carbon::today();

        for ($i = 0; $i < 14; $i++) {
            $dataVerificar = $hoje->copy()->addDays($i);
            $dataString = $dataVerificar->format('Y-m-d');

            if ($dataVerificar->isSunday()) {
                continue;
            }

            if ($estaDeCastigo) {
                $diasLotados[] = $dataString;
                continue;
            }

            $diaBloqueadoTodo = Bloqueio::where('data', $dataString)
                ->whereNull('horario_inicio')
                ->exists();

            if ($diaBloqueadoTodo) {
                $diasLotados[] = $dataString;
                continue; 
            }

            $limiteHorarios = 5; 

            $qtdAgendados = Agendamento::where('data_escolhida', $dataString)
                ->where('status', '!=', 'cancelado')
                ->count();

            if ($qtdAgendados >= $limiteHorarios) {
                $diasLotados[] = $dataString;
            }
        }

        $diaBloqueadoTotalmente = Bloqueio::where('data', $dataSelecionada)->whereNull('horario_inicio')->exists();

        if ($estudioFechado || $diaBloqueadoTotalmente || $estaDeCastigo) {
            $horariosDisponiveis = collect(); 
            $servicos = Servico::all();
            $manicures = User::all(); 
            $estudioFechado = true; 
        } else {
            $horariosOcupados = Agendamento::where('data_escolhida', $dataSelecionada)
                ->where('status', '!=', 'cancelado')
                ->pluck('hora_escolhida')
                ->map(function($hora) {
                    return \Carbon\Carbon::parse($hora)->format('H:i');
                })
                ->toArray();

            $horasPermitidas = $isSabado 
                ? ['08:00', '10:00', '12:00', '14:00', '16:00']
                : ['09:00', '11:00', '13:00', '15:00', '17:00'];

            $todosHorariosDoGrid = Horario::whereIn(DB::raw("DATE_FORMAT(hora, '%H:%i')"), $horasPermitidas)
                ->orderBy('hora')
                ->get();

            $bloqueiosParciais = Bloqueio::where('data', $dataSelecionada)
                ->whereNotNull('horario_inicio')
                ->whereNotNull('horario_fim')
                ->get();

            $horariosDisponiveis = $todosHorariosDoGrid->filter(function($item) use ($horariosOcupados, $bloqueiosParciais, $dataSelecionada) {
                $horaFormatada = Carbon::parse($item->hora)->format('H:i');

                if ($dataSelecionada === Carbon::today()->format('Y-m-d')) {
                    $agora = Carbon::now('-03:00')->format('H:i');
                    if ($horaFormatada <= $agora) {
                        return false; 
                    }
                }

                if (in_array($horaFormatada, $horariosOcupados)) {
                    return false;
                }

                foreach ($bloqueiosParciais as $bloqueio) {
                    $inicio = Carbon::parse($bloqueio->horario_inicio)->format('H:i');
                    $fim = Carbon::parse($bloqueio->horario_fim)->format('H:i');

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
            'manicureSelecionadaId',
            'estaDeCastigo'
        ));
    }

    /**
     * Processa o formulário de agendamento (Ação do Botão Confirmar)
     */
    /**
     * Processa o formulário de agendamento (Ação do Botão Confirmar)
     */
    public function salvarAgendamento(Request $request)
    {
        $usuarioLogado = Auth::user();
        
        // 1. Verifica se o usuário está suspenso (de castigo)
        if ($usuarioLogado && $usuarioLogado->bloqueado_ate && $usuarioLogado->bloqueado_ate->isFuture()) {
            return redirect()->back()->withErrors(['punicao' => 'Seu perfil está temporariamente impedido de realizar agendamentos.']);
        }

        $dadosValidados = $request->validate([
            'cliente_nome'     => 'required|string|max:255',
            'cliente_whatsapp' => 'nullable|string',
            'servico_id'       => 'required|exists:servicos,id',
            'manicure_id'      => 'required|exists:users,id',
            'data_escolhida'   => 'required|date',
            'hora_escolhida'   => 'required',
        ]);

        // 2. REGRA DE RESTRIÇÃO: Apenas 1 agendamento ativo por cliente
        // Verifica se o cliente já possui um agendamento pendente ou confirmado
        $agendamentoAtivo = Agendamento::whereIn('status', ['confirmado', 'pendente']) // Ajuste os status de agendamento ativo se necessário
            ->where(function($query) use ($usuarioLogado, $dadosValidados) {
                if ($usuarioLogado) {
                    // Se estiver logado, busca pelo ID de usuário ou pelo nome exato do perfil
                    $query->where('user_id', $usuarioLogado->id)
                          ->orWhere('cliente_nome', $usuarioLogado->name);
                } else {
                    // Se for agendamento sem login, busca pelo nome fornecido no input
                    $query->where('cliente_nome', $dadosValidados['cliente_nome']);
                }
            })
            ->first();

        if ($agendamentoAtivo) {
            $dataAtiva = Carbon::parse($agendamentoAtivo->data_escolhida)->format('d/m/Y');
            $horaAtiva = Carbon::parse($agendamentoAtivo->hora_escolhida)->format('H:i');
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['cliente_nome' => "Você já possui um agendamento ativo para o dia {$dataAtiva} às {$horaAtiva}h. Conclua ou cancele o atual antes de marcar um novo."]);
        }

        // 3. Validação de Bloqueio Administrativo de Horário
        $horaFormatada = Carbon::parse($dadosValidados['hora_escolhida'])->format('H:i');
        
        $estaBloqueado = Bloqueio::where('data', $dadosValidados['data_escolhida'])
            ->where(function($query) use ($horaFormatada) {
                $query->whereNull('horario_inicio')
                      ->orWhere(function($q) use ($horaFormatada) {
                          $q->where('horario_inicio', '<=', $horaFormatada)
                            ->where('horario_fim', '>', $horaFormatada);
                      });
            })->exists();

        if ($estaBloqueado) {
            return redirect()->back()->withErrors(['hora_escolhida' => 'Desculpe, este horário acabou de ser bloqueado administrativamente.']);
        }

        // 4. Criação do Agendamento
        Agendamento::create([
            'cliente_nome'     => $dadosValidados['cliente_nome'],
            'cliente_whatsapp' => $dadosValidados['cliente_whatsapp'] ?? 'Não informado',
            'servico_id'       => $dadosValidados['servico_id'],
            'manicure_id'      => $dadosValidados['manicure_id'],
            'data_escolhida'   => $dadosValidados['data_escolhida'],
            'hora_escolhida'   => $dadosValidados['hora_escolhida'],
            'status'           => 'confirmado',
            'user_id'          => $usuarioLogado ? $usuarioLogado->id : null,
        ]);

        return redirect()->route('home.index')->with('sucesso', 'Seu agendamento foi realizado com sucesso!');
    }

    public function meusAgendamentos()
    {
        $agendamentos = Agendamento::where('cliente_nome', auth()->user()->name)
            ->with(['servico', 'manicure'])
            ->orderBy('data_escolhida', 'desc')
            ->orderBy('hora_escolhida', 'desc')
            ->get();

        return view('cliente.agendamentos', compact('agendamentos'));
    }

    public function clienteCancela($id)
    {
        $agendamento = Agendamento::findOrFail($id);

        // 1. Segurança: Garante que o agendamento pertence ao usuário logado
        if ($agendamento->cliente_nome !== auth()->user()->name) {
            return redirect()->back()->with('error', 'Ação não autorizada.');
        }

        // 2. Regra de Negócio: Impede cancelamento com menos de 24h de antecedência
        // Mesclamos a data e a hora do agendamento para criar um objeto Carbon completo
        $dataHoraAgendamento = Carbon::parse($agendamento->data_escolhida . ' ' . $agendamento->hora_escolhida);

        // diffInHours com o segundo parâmetro como 'false' garante que se o horário 
        // já tiver passado ou estiver muito em cima, o valor retornado será menor que 24
        if (Carbon::now()->diffInHours($dataHoraAgendamento, false) < 24) {
            return redirect()->back()->with('error', 'O cancelamento só é permitido com no mínimo 24 horas de antecedência. Entre em contato conosco para suporte.');
        }

        // 3. Executa o cancelamento caso passe em todas as validações
        $agendamento->update([
            'status' => 'cancelado'
        ]);

        return redirect()->back()->with('success', 'Agendamento cancelado com sucesso!');
    }

    public function concluir(Request $request, $id)
    {
        $request->validate([
            'admin_password' => 'required',
        ]);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha incorreta! O atendimento não foi concluído.');
        }

        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update([
            'status' => 'concluido',
            'forma_pagamento' => $request->input('forma_pagamento', 'Não Informado')
        ]);

        return redirect()->back()->with('sucesso', 'Serviço concluído e pagamento registrado com sucesso!');
    }

    public function cancelar(Request $request, $id)
    {
        $request->validate([
            'admin_password' => 'required',
        ]);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha incorreta! O atendimento não foi concluído.');
        }

        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update(['status' => 'cancelado']);

        return redirect()->back()->with('sucesso', 'Agendamento cancelado.');
    }

    public function processarRemarcacao(Request $request, $id)
    {
        $request->validate([
            'admin_password' => 'required',
            'nova_data'      => 'required|date',
            'nova_hora'      => 'required',
        ]);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha administrativa incorreta! A remarcação não foi realizada.');
        }

        $agendamento = Agendamento::findOrFail($id);

        $agendamento->update([
            'data_escolhida' => $request->nova_data,
            'hora_escolhida' => $request->nova_hora,
            'status'         => 'confirmado'
        ]);

        return redirect()->route('admin.painel', ['data_escolhida' => $request->nova_data])
            ->with('sucesso', 'Agendamento remarcado com sucesso!');
    }

    /**
     * UNIFICADO: Registra falta com senha do administrador e roda a lógica de punição silenciosa de 60 dias
     */
    public function marcarFalta(Request $request, $id)
    {
        // 1. Validação da senha mestre do admin
        $request->validate([
            'admin_password' => 'required',
        ]);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha master inválida.');
        }

        // 2. Altera o status do agendamento para falta
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update([
            'status' => 'nao_compareceu'
        ]);

        // 3. Roda a verificação de histórico de faltas consecutivas do cliente
        $usuarioId = $agendamento->user_id;
        $usuario = null;

        if ($usuarioId) {
            $usuario = User::find($usuarioId);
        } else {
            $usuario = User::where('name', $agendamento->cliente_nome)->first();
        }

        if ($usuario) {
            // Busca os últimos 2 agendamentos resolvidos (concluidos ou faltas)
            $ultimosAgendamentos = Agendamento::where(function($query) use ($usuario) {
                    $query->where('user_id', $usuario->id)
                          ->orWhere('cliente_nome', $usuario->name);
                })
                ->whereIn('status', ['concluido', 'nao_compareceu'])
                ->orderBy('data_escolhida', 'desc')
                ->orderBy('hora_escolhida', 'desc')
                ->take(2)
                ->get();

            // Se existirem exatamente 2 registros e AMBOS forem falta ('nao_compareceu')
            if ($ultimosAgendamentos->count() === 2) {
                $primeiroStatus = $ultimosAgendamentos->get(0)->status;
                $segundoStatus = $ultimosAgendamentos->get(1)->status;

                if ($primeiroStatus === 'nao_compareceu' && $segundoStatus === 'nao_compareceu') {
                    // Aplica o "castigo" de 60 dias silenciosos a partir de agora
                    $usuario->bloqueado_ate = Carbon::now()->addDays(60);
                    $usuario->save();
                }
            }
        }

        return redirect()->back()->with('sucesso', 'Falta registrada e histórico analisado com sucesso!');
    }

    /**
     * Exibe a listagem de clientes que estão atualmente suspensas (de castigo)
     */
    public function clientesSuspensos()
    {
        $clientes = User::whereNotNull('bloqueado_ate')
            ->where('bloqueado_ate', '>', Carbon::now())
            ->orderBy('bloqueado_ate', 'asc')
            ->get();

        return view('admin.clientes_suspensos', compact('clientes'));
    }

    /**
     * Desbloqueia a cliente limpando o campo 'bloqueado_ate' antes do prazo de 60 dias terminar
     */
    public function desbloquearCliente(Request $request, $id)
    {
        // 1. Valida se a senha administrativa foi enviada
        $request->validate([
            'admin_password' => 'required',
        ]);

        // 2. Valida se a senha confere com a do admin logado
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha master inválida! A cliente não foi desbloqueada.');
        }

        // 3. Remove o castigo limpando o bloqueio
        $cliente = User::findOrFail($id);
        $cliente->bloqueado_ate = null;
        $cliente->save();

        return redirect()->back()->with('sucesso', "O agendamento online de {$cliente->name} foi liberado com sucesso!");
    }

    public function graficosMensais()
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        // 1. Métricas de Status (Gráfico 1)
        $dadosStatus = DB::table('agendamentos')
            ->select('status', DB::raw('count(*) as total'))
            ->whereBetween('data_escolhida', [$inicioMes, $fimMes])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $statusPadrao = ['concluido' => 0, 'cancelado' => 0, 'nao_compareceu' => 0, 'remarcado' => 0];
        $metricas = array_merge($statusPadrao, $dadosStatus);

        // 2. Métricas Financeiras (Gráfico 2)
        // Soma o valor de todos os agendamentos com status 'concluido' no mês
        $faturamentoTotal = DB::table('agendamentos')
        ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id') // Junção das tabelas
        ->where('agendamentos.status', 'concluido')
        ->whereBetween('agendamentos.data_escolhida', [$inicioMes, $fimMes])
        ->sum('servicos.preco'); // Certifique-se de que o nome da coluna de preço é 'valor'

        // Soma as despesas registradas no mês (ajuste de acordo com sua tabela de despesas)
        $despesasTotal = DB::table('despesas') // Substitua pelo nome correto da sua tabela de despesas
            ->whereBetween('data_vencimento', [$inicioMes, $fimMes]) // ou 'created_at'
            ->sum('valor');

        // Calcula o Lucro Líquido
        $lucroLiquido = max(0, $faturamentoTotal - $despesasTotal);

        return view('admin.graficos', compact('metricas', 'faturamentoTotal', 'despesasTotal', 'lucroLiquido'));
    }
}