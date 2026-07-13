<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Despesa;
use App\Models\Servico;
use App\Models\Bloqueio; 
use App\Models\Configuracao;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $hoje = Carbon::today()->format('Y-m-d');
        $fimDuasSemanas = Carbon::today()->addDays(13)->format('Y-m-d');

        // Captura os novos inputs do filtro de histórico
        $nome = $request->input('nome');
        $dataManual = $request->input('data_manual'); // Recebe o formato Y-m-d do hidden input

        // Define a data que ficará ativa visualmente
        if (!empty($dataManual)) {
            $dataSelecionada = $dataManual;
        } else {
            $dataSelecionada = $request->get('data_escolhida', $hoje);
        }

        // 1. Inicia a Query de busca dos agendamentos
        $query = Agendamento::with(['servico', 'manicure']);

        // Se houver busca por nome, filtra por nome e traz TODOS os status (Histórico Geral)
        if (!empty($nome)) {
            $query->where('cliente_nome', 'LIKE', "%{$nome}%");
            
            // Se também preencheu a data manual com o nome, restringe àquela data
            if (!empty($dataManual)) {
                $query->whereDate('data_escolhida', $dataManual);
            }
        } else {
            // Se NÃO buscou por nome, funciona no modo padrão diário (Filtra por Data)
            $query->where('data_escolhida', $dataSelecionada);
            
            // Se for a listagem padrão do carrossel (sem data manual), esconde os cancelados
            if (empty($dataManual)) {
                $query->where('status', '!=', 'cancelado');
            }
        }

        // Executa a busca ordenando por hora
        $agendamentos = $query->orderBy('hora_escolhida', 'asc')->get();

        // 2. Conta o total de agendamentos dentro do intervalo das 2 semanas do grid
        $totalDuasSemanas = Agendamento::whereBetween('data_escolhida', [$hoje, $fimDuasSemanas])
            ->where('status', '!=', 'cancelado')
            ->count();

        // 🔥 3. Lógica para calcular quais dos próximos 14 dias estão lotados ou bloqueados (Mantida Intacta)
        $diasLotados = [];
        $limiteHorarios = 5; 

        for ($i = 0; $i < 14; $i++) {
            $dataVerificar = Carbon::today()->addDays($i);
            $dataString = $dataVerificar->format('Y-m-d');

            if ($dataVerificar->isSunday()) {
                continue;
            }

            $bloqueioTotal = Bloqueio::where('data', $dataString)
                ->whereNull('horario_inicio')
                ->exists();

            if ($bloqueioTotal) {
                $diasLotados[] = $dataString;
                continue;
            }

            $bloqueiosParciais = Bloqueio::where('data', $dataString)
                ->whereNotNull('horario_inicio')
                ->get();

            $isSabado = $dataVerificar->isSaturday();
            $horasPermitidas = $isSabado 
                ? ['08:00', '10:00', '12:00', '14:00', '16:00']
                : ['09:00', '11:00', '13:00', '15:00', '17:00'];

            $totalHorariosPossiveis = count($horasPermitidas);
            $horariosBloqueadosQtd = 0;

            foreach ($horasPermitidas as $hora) {
                foreach ($bloqueiosParciais as $b) {
                    if ($hora >= $b->horario_inicio && $hora < $b->horario_fim) {
                        $horariosBloqueadosQtd++;
                        break;
                    }
                }
            }

            $qtdAgendados = Agendamento::where('data_escolhida', $dataString)
                ->where('status', '!=', 'cancelado')
                ->count();

            if (($qtdAgendados + $horariosBloqueadosQtd) >= $totalHorariosPossiveis || $qtdAgendados >= $limiteHorarios) {
                $diasLotados[] = $dataString;
            }
        }

        return view('admin.painel', compact('agendamentos', 'dataSelecionada', 'totalDuasSemanas', 'diasLotados'));
    }

    public function concluir(Request $request, $id)
    {
        // 1. Valida se a senha foi enviada
        $request->validate([
            'admin_password' => 'required',
        ]);

        // 2. Verifica se a senha informada bate com a do admin logado
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha incorreta! O atendimento não foi concluído.');
        }

        // 3. Encontra o agendamento e atualiza usando ->input()
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update([
            'status' => 'concluido',
            'forma_pagamento' => $request->input('forma_pagamento', 'Não Informado')
        ]);

        return redirect()->back()->with('sucesso', 'Serviço concluído e pagamento registrado com sucesso!');
    }

    public function cancelar(Request $request, $id)
    {
        // 1. Valida se a senha foi enviada
        $request->validate([
            'admin_password' => 'required',
        ]);

        // 2. Verifica se a senha informada bate com a do admin logado
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha incorreta! O atendimento não foi concluído.');
        }
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update(['status' => 'cancelado',]);

        return redirect()->back()->with('sucesso', 'Agendamento cancelado.');
    }

    public function processarRemarcacao(Request $request, $id)
    {
        // 1. Valida a senha do admin logado e os novos dados do horário
        $request->validate([
            'admin_password' => 'required',
            'nova_data'      => 'required|date',
            'nova_hora'      => 'required',
        ]);

        // Verificar se a senha informada bate com a do admin atual
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->back()->with('erro', 'Senha administrativa incorreta! A remarcação não foi realizada.');
        }

        // 2. Encontra o agendamento antigo
        $agendamento = Agendamento::findOrFail($id);

        // 3. Atualiza com a nova data e horário (o horário antigo fica vago automaticamente)
        $agendamento->update([
            'data_escolhida' => $request->nova_data,
            'hora_escolhida' => $request->nova_hora,
            'status'         => 'confirmado' // Garante que volta a ficar confirmado se estivesse diferente
        ]);

        return redirect()->route('admin.painel', ['data_escolhida' => $request->nova_data])
            ->with('sucesso', 'Agendamento remarcado com sucesso!');
    }

    public function faturamento(Request $request)
    {
        // Captura o mês e ano filtrado ou assume o mês atual
        $mesAno = $request->get('mes', date('Y-m')); 
        $ano = date('Y', strtotime($mesAno));
        $mes = date('m', strtotime($mesAno));

        // 1. Faturamento Total por Forma de Pagamento no mês
        $porFormaPagamento = Agendamento::join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->where('agendamentos.status', 'concluido')
            ->whereYear('agendamentos.data_escolhida', $ano)
            ->whereMonth('agendamentos.data_escolhida', $mes)
            ->select('agendamentos.forma_pagamento', DB::raw('SUM(servicos.preco) as total'))
            ->groupBy('agendamentos.forma_pagamento')
            ->get();

        // Total Bruto (Entradas)
        $totalGeral = $porFormaPagamento->sum('total');

        // 2. Total de Despesas (Saídas) no mesmo mês
        $totalDespesas = Despesa::whereYear('data_vencimento', $ano)
            ->whereMonth('data_vencimento', $mes)
            ->sum('valor');

        // 3. Lucro Líquido Comercial
        $lucroLiquido = $totalGeral - $totalDespesas;

        // 4. Faturamento Diário do mês selecionado
        $faturamentoDiario = Agendamento::join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->where('agendamentos.status', 'concluido')
            ->whereYear('agendamentos.data_escolhida', $ano)
            ->whereMonth('agendamentos.data_escolhida', $mes)
            ->select('agendamentos.data_escolhida', DB::raw('SUM(servicos.preco) as total'), DB::raw('COUNT(agendamentos.id) as qtd'))
            ->groupBy('agendamentos.data_escolhida')
            ->orderBy('agendamentos.data_escolhida', 'asc')
            ->get();

        // 5. Faturamento por Manicure no mês
        $porManicure = Agendamento::join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->join('users', 'agendamentos.manicure_id', '=', 'users.id')
            ->where('agendamentos.status', 'concluido')
            ->whereYear('agendamentos.data_escolhida', $ano)
            ->whereMonth('agendamentos.data_escolhida', $mes)
            ->select('users.name as manicure', DB::raw('SUM(servicos.preco) as total'), DB::raw('COUNT(agendamentos.id) as qtd'))
            ->groupBy('users.name')
            ->get();

        return view('admin.faturamento', compact(
            // ... (restante mantém igual)
            'porFormaPagamento', 
            'faturamentoDiario', 
            'porManicure', 
            'totalGeral', 
            'totalDespesas', 
            'lucroLiquido', 
            'mesAno'
        ));
    }

    public function listarDespesas(Request $request)
    {
        $mesAno = $request->get('mes', date('Y-m'));
        $ano = date('Y', strtotime($mesAno));
        $mes = date('m', strtotime($mesAno));

        // Busca as despesas do mês filtrado
        $despesas = Despesa::whereYear('data_vencimento', $ano)
            ->whereMonth('data_vencimento', $mes)
            ->orderBy('data_vencimento', 'desc')
            ->get();

        $totalDespesas = $despesas->sum('valor');

        // Traz apenas os meses/anos que possuem despesas no banco
        $mesesComDados = Despesa::select(
                DB::raw("DATE_FORMAT(data_vencimento, '%Y-%m') as mes_ano"),
                DB::raw("DATE_FORMAT(data_vencimento, '%m') as numero_mes"),
                DB::raw("DATE_FORMAT(data_vencimento, '%Y') as ano")
            )
            ->groupBy('mes_ano', 'numero_mes', 'ano')
            ->orderBy('mes_ano', 'desc')
            ->get();

        // Se o mês atual não tiver dados mas for o selecionado, garantimos que ele apareça na lista de opções
        if (!$mesesComDados->contains('mes_ano', $mesAno)) {
            $mesesComDados->prepend((object)[
                'mes_ano' => $mesAno,
                'numero_mes' => $mes,
                'ano' => $ano
            ]);
        }

        return view('admin.despesas', compact('despesas', 'totalDespesas', 'mesAno', 'mesesComDados'));
    }

    public function salvarDespesa(Request $request)
    {
        $dados = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'data_vencimento' => 'required|date',
            'categoria' => 'required|string',
        ]);

        Despesa::create($dados);

        return redirect()->back()->with('sucesso', 'Despesa lançada com sucesso!');
    }

    public function deletarDespesa($id)
    {
        $despesa = Despesa::findOrFail($id);
        $despesa->delete();

        return redirect()->back()->with('sucesso', 'Despesa removida!');
    }

    public function atualizarDespesa(Request $request, $id)
    {
        $despesa = Despesa::findOrFail($id);
        $despesa->update($request->all());

        return redirect()->back()->with('sucesso', 'Despesa atualizada com sucesso!');
    }

    public function listarServicos()
    {
        $servicos = Servico::orderBy('nome', 'asc')->get();
        return view('admin.servicos', compact('servicos'));
    }

    public function salvarServico(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'foto_exemplo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto_exemplo')) {
            $dados['foto_exemplo'] = $request->file('foto_exemplo')->store('servicos', 'public');
        }

        Servico::create($dados);

        return redirect()->back()->with('sucesso', 'Serviço cadastrado com sucesso!');
    }

    public function deletarServico($id)
    {
        $servico = Servico::findOrFail($id);

        if ($servico->foto_exemplo) {
            Storage::disk('public')->delete($servico->foto_exemplo);
        }

        $servico->delete();

        return redirect()->back()->with('sucesso', 'Serviço excluído com sucesso!');
    }

    public function atualizarServico(Request $request, $id)
    {
        $servico = Servico::findOrFail($id);

        $dados = $request->validate([
            'nome'            => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'preco'           => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:1',
            'foto_exemplo'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto_exemplo')) {
            if ($servico->foto_exemplo) {
                Storage::disk('public')->delete($servico->foto_exemplo);
            }
            $dados['foto_exemplo'] = $request->file('foto_exemplo')->store('servicos', 'public');
        }

        $servico->update($dados);

        return redirect()->back()->with('sucesso', 'Serviço updated com sucesso!');
    }
}