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
use Illuminate\Support\Facades\Hash;

class AgendamentoController extends Controller
{
    /**
     * Exibe a tela de seleção de horários para as próximas 2 semanas
     */
    public function escolherHorario(Request $request)
    {
        $usuarioLogado = Auth::user();
        $estaDeCastigo = $usuarioLogado && $usuarioLogado->bloqueado_ate && $usuarioLogado->bloqueado_ate->isFuture();

        $servicoSelecionadoId  = $request->input('servico_id');
        $manicureSelecionadaId = $request->input('manicure_id');

        $dataSelecionada = $request->get('data_escolhida', Carbon::today()->format('Y-m-d'));
        $carbonData      = Carbon::parse($dataSelecionada);

        $estudioFechado = $carbonData->isSunday();
        $isSabado       = $carbonData->isSaturday();

        // 1. Carregamento de serviços e manicures
        $servicos  = Servico::all();
        $manicures = User::where('role', 'manicure')->get();

        $diasLotados = [];
        $hoje        = Carbon::today();

        // 2. Verificação de ocupação nos próximos 14 dias
        for ($i = 0; $i < 14; $i++) {
            $dataVerificar = $hoje->copy()->addDays($i);
            $dataString    = $dataVerificar->format('Y-m-d');

            if ($dataVerificar->isSunday()) {
                continue;
            }

            if ($estaDeCastigo) {
                $diasLotados[] = $dataString;
                continue;
            }

            $diaBloqueadoTodo = Bloqueio::whereDate('data', $dataString)
                ->whereNull('horario_inicio')
                ->exists();

            if ($diaBloqueadoTodo) {
                $diasLotados[] = $dataString;
                continue; 
            }

            $horasPermitidas = $dataVerificar->isSaturday() 
                ? ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00']
                : ['09:00', '11:00', '13:00', '15:00', '17:00'];

            $totalHorariosNoGrid = count($horasPermitidas);

            if ($manicureSelecionadaId) {
                $qtdAgendados = Agendamento::whereDate('data_escolhida', $dataString)
                    ->where('manicure_id', $manicureSelecionadaId)
                    ->where('status', '!=', 'cancelado')
                    ->count();

                if ($qtdAgendados >= $totalHorariosNoGrid) {
                    $diasLotados[] = $dataString;
                }
            } else {
                $totalManicures = $manicures->count() ?: 1;
                $capacidadeTotalEstudio = $totalHorariosNoGrid * $totalManicures;

                $qtdAgendadosGeral = Agendamento::whereDate('data_escolhida', $dataString)
                    ->where('status', '!=', 'cancelado')
                    ->count();

                if ($qtdAgendadosGeral >= $capacidadeTotalEstudio) {
                    $diasLotados[] = $dataString;
                }
            }
        }

        $diaBloqueadoTotalmente = Bloqueio::where('data', $dataSelecionada)->whereNull('horario_inicio')->exists();

        // 3. Montagem dos horários do dia selecionado
        if ($estudioFechado || $diaBloqueadoTotalmente || $estaDeCastigo) {
            $horariosDisponiveis = collect(); 
            $estudioFechado      = true; 
        } else {
            $queryOcupados = Agendamento::where('data_escolhida', $dataSelecionada)
                ->where('status', '!=', 'cancelado');

            if (session()->has('remarcacao_agendamento_id')) {
                $queryOcupados->where('id', '!=', session('remarcacao_agendamento_id'));
            }

            if ($manicureSelecionadaId) {
                $queryOcupados->where('manicure_id', $manicureSelecionadaId);
            }

            $horariosOcupados = $queryOcupados->pluck('hora_escolhida')
                ->map(fn($hora) => Carbon::parse($hora)->format('H:i'))
                ->toArray();

            $horasPermitidas = $isSabado 
                ? ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00']
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
                    $fim    = Carbon::parse($bloqueio->horario_fim)->format('H:i');

                    if ($horaFormatada >= $inicio && $horaFormatada < $fim) {
                        return false; 
                    }
                }

                return true; 
            });
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
     * Inicia o fluxo de remarcação pelo cliente
     */
    public function iniciarRemarcacao($id)
    {
        $agendamento = Agendamento::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['agendado', 'remarcado'])
            ->first();

        if (!$agendamento) {
            return redirect()->back()->with('error', 'Este agendamento não pode mais ser remarcado.');
        }

        if ($agendamento->qtd_remarcacoes >= 1) {
            return redirect()->back()->with('error', 'Você já remarcou este agendamento 1 vez. Não é permitido remarcar novamente.');
        }

        session([
            'remarcacao_agendamento_id' => $agendamento->id,
            'agendamento_cliente_id'    => $agendamento->user_id
        ]);

        return redirect()->route('agendamento.horarios', [
            'servico_id'  => $agendamento->servico_id,
            'manicure_id' => $agendamento->manicure_id,
        ])->with('info', 'Escolha a nova data e horário para o seu atendimento.');
    }

    /**
     * Cria um novo agendamento ou atualiza um existente (Remarcação do cliente)
     */
    public function salvarAgendamento(Request $request)
    {
        $usuarioLogado = Auth::user();
        
        $clienteParaAgendamento = session()->has('agendamento_cliente_id')
            ? User::find(session('agendamento_cliente_id'))
            : null;

        $isAdminAgendando = $usuarioLogado && $usuarioLogado->hasRole('admin');
        $usuarioAlvo = $clienteParaAgendamento ?? $usuarioLogado;

        if (!$isAdminAgendando && $usuarioAlvo && $usuarioAlvo->bloqueado_ate && $usuarioAlvo->bloqueado_ate->isFuture()) {
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

        $isRemarcacao = session()->has('remarcacao_agendamento_id');
        $agendamentoIdRemarcar = session('remarcacao_agendamento_id');

        // Impede choque de horário para a mesma manicure
        $queryOcupado = Agendamento::where('manicure_id', $dadosValidados['manicure_id'])
            ->where('data_escolhida', $dadosValidados['data_escolhida'])
            ->where('hora_escolhida', $dadosValidados['hora_escolhida'])
            ->whereIn('status', ['agendado', 'remarcado']);

        if ($isRemarcacao) {
            $queryOcupado->where('id', '!=', $agendamentoIdRemarcar);
        }

        if ($queryOcupado->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['hora_escolhida' => 'A profissional escolhida já possui um atendimento neste mesmo dia e horário. Escolha outro horário.']);
        }

        // Restrição de 1 agendamento ativo por cliente
        if (!$isAdminAgendando && !$isRemarcacao) {
            $agendamentoAtivo = Agendamento::whereIn('status', ['agendado', 'remarcado'])
                ->where(function($query) use ($usuarioAlvo, $dadosValidados) {
                    if ($usuarioAlvo) {
                        $query->where('user_id', $usuarioAlvo->id)
                              ->orWhere('cliente_nome', $usuarioAlvo->name);
                    } else {
                        $query->where('cliente_nome', $dadosValidados['cliente_nome']);
                    }
                })
                ->first();

            if ($agendamentoAtivo) {
                $dataAtiva = Carbon::parse($agendamentoAtivo->data_escolhida)->format('d/m/Y');
                $horaAtiva = Carbon::parse($agendamentoAtivo->hora_escolhida)->format('H:i');
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['cliente_nome' => "Você já possui um agendamento ativo para o dia {$dataAtiva} às {$horaAtiva}h."]);
            }
        }

        // Validação de bloqueios de agenda
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
            return redirect()->back()->withErrors(['hora_escolhida' => 'Desculpe, este horário está bloqueado para agendamentos.']);
        }

        // Execução da remarcação
        if ($isRemarcacao) {
            $agendamentoExistente = Agendamento::find($agendamentoIdRemarcar);

            if ($agendamentoExistente) {
                $dataOrig = $agendamentoExistente->data_original ?? $agendamentoExistente->data_escolhida;
                $horaOrig = $agendamentoExistente->hora_original ?? $agendamentoExistente->hora_escolhida;

                $agendamentoExistente->update([
                    'servico_id'      => $dadosValidados['servico_id'],
                    'manicure_id'     => $dadosValidados['manicure_id'],
                    'data_escolhida'  => $dadosValidados['data_escolhida'],
                    'hora_escolhida'  => $dadosValidados['hora_escolhida'],
                    'status'          => 'remarcado',
                    'qtd_remarcacoes' => 1,
                    'data_original'   => $dataOrig,
                    'hora_original'   => $horaOrig,
                ]);
            }

            session()->forget(['remarcacao_agendamento_id', 'agendamento_cliente_id']);

            return redirect()->route('cliente.agendamentos')
                ->with('sucesso', 'Seu agendamento foi remarcado com sucesso!');
        }

        // Criação do novo agendamento
        Agendamento::create([
            'cliente_nome'     => $clienteParaAgendamento ? $clienteParaAgendamento->name : $dadosValidados['cliente_nome'],
            'cliente_whatsapp' => $clienteParaAgendamento ? ($clienteParaAgendamento->telefone ?? $dadosValidados['cliente_whatsapp']) : ($dadosValidados['cliente_whatsapp'] ?? 'Não informado'),
            'servico_id'       => $dadosValidados['servico_id'],
            'manicure_id'      => $dadosValidados['manicure_id'],
            'data_escolhida'   => $dadosValidados['data_escolhida'],
            'hora_escolhida'   => $dadosValidados['hora_escolhida'],
            'status'           => 'agendado',
            'user_id'          => $usuarioAlvo ? $usuarioAlvo->id : null,
        ]);

        if (session()->has('agendamento_cliente_id')) {
            session()->forget('agendamento_cliente_id');
            
            return redirect()->route('admin.painel', ['data' => $dadosValidados['data_escolhida']])
                ->with('success', "Agendamento realizado com sucesso para {$usuarioAlvo->name} no dia " . Carbon::parse($dadosValidados['data_escolhida'])->format('d/m/Y') . "!");
        }

        return redirect()->route('home.index')->with('sucesso', 'Seu agendamento foi realizado com sucesso!');
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
            'status'         => 'remarcado',
            'is_remarcado'   => true,
        ]);

        return redirect()->route('admin.painel', ['data' => $request->nova_data])
            ->with('sucesso', 'Agendamento remarcado com sucesso!');
    }

    public function meusAgendamentos($id = null)
    {
        $usuario = ($id && auth()->user()->hasRole('admin')) 
            ? User::findOrFail($id) 
            : auth()->user();

        $agendamentos = Agendamento::where('user_id', $usuario->id)
            ->with(['servico', 'manicure'])
            ->orderBy('data_escolhida', 'desc')
            ->orderBy('hora_escolhida', 'desc')
            ->get();

        return view('cliente.agendamentos', compact('agendamentos', 'usuario'));
    }

    public function clienteCancela($id)
    {
        $agendamento = Agendamento::findOrFail($id);

        if ($agendamento->user_id !== auth()->id() && $agendamento->cliente_nome !== auth()->user()->name) {
            return redirect()->back()->with('error', 'Ação não autorizada.');
        }

        $agendamento->update(['status' => 'cancelado']);

        return redirect()->back()->with('success', 'Agendamento cancelado com sucesso!');
    }

    public function concluir(Request $request, $id)
    {
        $request->validate(['admin_password' => 'required']);

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
        $request->validate(['admin_password' => 'required']);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha incorreta!');
        }

        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update(['status' => 'cancelado']);

        return redirect()->back()->with('sucesso', 'Agendamento cancelado.');
    }

    public function marcarFalta(Request $request, $id)
    {
        $request->validate(['admin_password' => 'required']);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha master inválida.');
        }

        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update(['status' => 'nao_compareceu']);

        $usuarioId = $agendamento->user_id;
        $usuario = $usuarioId ? User::find($usuarioId) : User::where('name', $agendamento->cliente_nome)->first();

        if ($usuario) {
            $ultimosAgendamentos = Agendamento::where(function($query) use ($usuario) {
                    $query->where('user_id', $usuario->id)
                          ->orWhere('cliente_nome', $usuario->name);
                })
                ->whereIn('status', ['concluido', 'nao_compareceu'])
                ->orderBy('data_escolhida', 'desc')
                ->orderBy('hora_escolhida', 'desc')
                ->take(2)
                ->get();

            if ($ultimosAgendamentos->count() === 2) {
                if ($ultimosAgendamentos->get(0)->status === 'nao_compareceu' && 
                    $ultimosAgendamentos->get(1)->status === 'nao_compareceu') {
                    
                    $usuario->bloqueado_ate = Carbon::now()->addDays(60);
                    $usuario->save();
                }
            }
        }

        return redirect()->back()->with('sucesso', 'Falta registrada e histórico analisado com sucesso!');
    }

    public function criarParaUsuario($userId)
    {
        $cliente = User::findOrFail($userId);
        session(['agendamento_cliente_id' => $cliente->id]);

        return redirect()->route('agendamento.horarios')
            ->with('info', "Agendando horário em nome de: {$cliente->name}");
    }

    public function clientesSuspensos()
    {
        $clientes = User::whereNotNull('bloqueado_ate')
            ->where('bloqueado_ate', '>', Carbon::now())
            ->orderBy('bloqueado_ate', 'asc')
            ->get();

        return view('admin.clientes_suspensos', compact('clientes'));
    }

    public function desbloquearCliente(Request $request, $id)
    {
        $request->validate(['admin_password' => 'required']);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha master inválida! A cliente não foi desbloqueada.');
        }

        $cliente = User::findOrFail($id);
        $cliente->bloqueado_ate = null;
        $cliente->save();

        return redirect()->back()->with('sucesso', "O agendamento online de {$cliente->name} foi liberado com sucesso!");
    }

    public function graficosMensais(Request $request)
    {
        $mes = $request->get('mes', Carbon::now()->format('m'));
        $ano = $request->get('ano', Carbon::now()->format('Y'));

        $inicioMes = Carbon::createFromDate($ano, $mes, 1)->startOfMonth();
        $fimMes = Carbon::createFromDate($ano, $mes, 1)->endOfMonth();

        // 1. Métricas de Status
        $agendamentos = DB::table('agendamentos')
            ->whereBetween('data_escolhida', [$inicioMes, $fimMes])
            ->get();

        $metricas = [
            'concluido'      => 0,
            'cancelado'      => 0,
            'nao_compareceu' => 0,
            'remarcado'      => 0,
        ];

        foreach ($agendamentos as $agendamento) {
            $statusSlug = strtolower(trim($agendamento->status));
            $foiRemarcado = (!empty($agendamento->is_remarcado) && $agendamento->is_remarcado == 1) || $statusSlug === 'remarcado';

            if ($foiRemarcado) {
                $metricas['remarcado']++;
            } elseif (array_key_exists($statusSlug, $metricas)) {
                $metricas[$statusSlug]++;
            }
        }

        // 2. Métricas Financeiras
        $faturamentoTotal = DB::table('agendamentos')
            ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->whereBetween('agendamentos.data_escolhida', [$inicioMes, $fimMes])
            ->where(function($query) {
                $query->where('agendamentos.status', 'concluido')
                      ->orWhere('agendamentos.is_remarcado', 1);
            })
            ->sum('servicos.preco');

        $despesasTotal = DB::table('despesas')
            ->whereBetween('data_vencimento', [$inicioMes, $fimMes])
            ->sum('valor');

        $lucroLiquido = max(0, $faturamentoTotal - $despesasTotal);

        // 3. Métricas de Serviços
        $servicosRealizados = DB::table('agendamentos')
            ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->select('servicos.nome as servico', DB::raw('count(*) as total'))
            ->whereBetween('agendamentos.data_escolhida', [$inicioMes, $fimMes])
            ->where(function($query) {
                $query->where('agendamentos.status', 'concluido')
                      ->orWhere('agendamentos.is_remarcado', 1);
            })
            ->groupBy('servicos.nome')
            ->orderByDesc('total')
            ->get();

        $servicosLabels = $servicosRealizados->pluck('servico')->toArray();
        $servicosTotais = $servicosRealizados->pluck('total')->toArray();

        return view('admin.graficos', compact(
            'metricas', 
            'faturamentoTotal', 
            'despesasTotal', 
            'lucroLiquido',
            'servicosLabels',
            'servicosTotais'
        ));
    }
}