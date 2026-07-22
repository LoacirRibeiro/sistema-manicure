<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.3); }
        .bg-neon { background-color: #FF007F; }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex flex-col justify-between">

    {{-- Header --}}
    <header class="p-4 md:p-6 bg-zinc-950/80 border-b border-zinc-900 flex justify-between items-center backdrop-blur-md sticky top-0 z-50 gap-2">
    
        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <a href="#" class="text-lg md:text-xl font-black tracking-tighter uppercase whitespace-nowrap">
                Nails<span class="text-neon">Studio</span>
            </a>
        </div>

        {{-- Área do Usuário e Ação --}}
        <div class="flex items-center gap-2 sm:gap-4">
            
            {{-- Lógica para Primeiro e Último Nome --}}
            @php
                $partesNome = explode(' ', trim(auth()->user()->name ?? ''));
                $primeiroNome = $partesNome[0] ?? '';
                $ultimoNome = count($partesNome) > 1 ? end($partesNome) : '';
                $nomeExibicao = trim("$primeiroNome $ultimoNome");
            @endphp

            {{-- Nome do Usuário (Responsivo) --}}
            <span class="text-xs sm:text-sm text-zinc-400 max-w-[120px] sm:max-w-none truncate">
                <span class="hidden sm:inline">Olá, </span>
                <strong class="text-zinc-200 font-semibold">{{ $nomeExibicao }}</strong>
            </span>

            {{-- Botão Sair --}}
            <a href="{{ route('home.index') }}" class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider border border-pink-500/30 bg-pink-500/10 text-neon px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-neon hover:text-white hover:shadow-[0_0_15px_rgba(255,0,127,0.5)] flex items-center justify-center gap-1.5 whitespace-nowrap">
                <span class="hidden sm:inline">Sair do Painel</span>
                <span class="sm:hidden">Sair</span>
            </a>
        </div>

    </header>

    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-3">
        <div class="flex justify-start mt-4 ml-4">
            <span class="text-xs uppercase tracking-wider bg-red-950/50 px-3 py-1 rounded-full text-red-400 border border-red-900/50">
                Painel de Controle 
            </span>
        </div>
    </div>

    {{-- Container com o botão de adicionar serviços (Apenas visível para Admin) --}}
    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <div class="w-full max-w-6xl mx-auto mt-6 mb-6 px-4 sm:px-6">
            <div class="card-glass p-6 rounded-3xl flex flex-col gap-6 border border-zinc-800/80 bg-zinc-900/10">
                
                {{-- Bloco de Texto e Ícone --}}
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-2xl bg-pink-500/10 flex items-center justify-center text-neon text-2xl shrink-0">
                        <i class="la la-cog"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-white">Painel de Controle</h4>
                        <p class="text-xs text-zinc-400 mt-0.5">Como administrador, você pode gerenciar faturamentos, extrair relatórios mensais de atendimentos, além de gerenciar os procedimentos do site.</p>
                    </div>
                </div>

                {{-- Linha divisória sutil --}}
                <div class="h-[1px] w-full bg-zinc-850"></div>

                {{-- Botões de Ação Alinhados Abaixo --}}
                <div class="flex flex-col sm:flex-row items-center gap-3">

                    {{-- Botão Gestão de Clientes --}}
                    <a href="{{ route('admin.usuarios.index') }}" class="w-full sm:w-auto text-xs font-semibold uppercase tracking-wider border border-purple-500/30 bg-purple-500/10 text-purple-400 px-5 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-purple-600 hover:text-white hover:shadow-[0_0_15px_rgba(168,85,247,0.5)] flex items-center justify-center gap-1.5">
                        <i class="la la-users text-base"></i> Gestão de Clientes
                    </a>

                    <a href="{{ route('admin.graficos') }}" class="w-full sm:w-auto text-xs font-semibold uppercase tracking-wider border border-cyan-500/30 bg-cyan-500/10 text-cyan-400 px-5 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-cyan-500 hover:text-white hover:shadow-[0_0_15px_rgba(6,182,212,0.5)] flex items-center justify-center gap-1.5">
                        <i class="la la-chart-pie text-base"></i> Desempenho Mensal
                    </a>

                    {{-- Botão Faturamento --}}
                    <a href="{{ route('admin.faturamento') }}" class="w-full sm:w-auto text-xs font-semibold uppercase tracking-wider border border-zinc-800 bg-zinc-900/50 text-zinc-300 px-5 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-zinc-800 hover:text-white flex items-center justify-center gap-1.5">
                        <i class="la la-wallet text-base"></i> Faturamento
                    </a>
                </div>

            </div>
        </div>
    @endif

    {{-- FILTRO DE HISTÓRICO COM FORMATO BR --}}
    <section class="w-full bg-zinc-950/40 border-b border-zinc-900/60 py-6 px-4 md:px-10">
        <div class="max-w-6xl w-full mx-auto mt-6 mb-6 px-4 sm:px-6">
            <form id="filtroHistoricoForm" action="{{ route('admin.painel') }}" method="GET" onsubmit="prepararEnvioData(event)" class="card-glass p-5 rounded-2xl grid grid-cols-1 md:grid-cols-3 gap-4 items-end border border-zinc-800/60">
                
                {{-- Filtro por Nome --}}
                <div class="space-y-1.5">
                    <label for="buscar_nome" class="block text-xs uppercase tracking-wider text-zinc-400 font-semibold">Buscar por Nome</label>
                    <div class="relative">
                        <i class="la la-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-base"></i>
                        <input type="text" id="buscar_nome" name="nome" value="{{ request('nome') }}" placeholder="Nome da cliente..." class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl pl-9 pr-4 py-2.5 text-sm focus:border-pink-500 focus:outline-none placeholder-zinc-600 transition-all">
                    </div>
                </div>

                {{-- Filtro por Qualquer Data Passada - Formato BR --}}
                <div class="space-y-1.5">
                    <label for="input_data_br" class="block text-xs uppercase tracking-wider text-zinc-400 font-semibold">Buscar por Data</label>
                    <div class="relative">
                        <i class="la la-calendar absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-base"></i>
                        
                        @php
                            $dataExibicao = request('data_manual') ? \Carbon\Carbon::parse(request('data_manual'))->format('d/m/Y') : '';
                        @endphp
                        <input type="text" id="input_data_br" value="{{ $dataExibicao }}" placeholder="Ex: 25/12/2024" maxlength="10" oninput="mascaraData(this)" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl pl-9 pr-4 py-2.5 text-sm focus:border-pink-500 focus:outline-none placeholder-zinc-600 transition-all">
                        
                        <input type="hidden" id="hidden_data_manual" name="data_manual" value="{{ request('data_manual') }}">
                    </div>
                </div>

                {{-- Botões de Ação --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-grow bg-emerald-600 hover:bg-emerald-700 border border-emerald-500 text-white font-bold py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-lg shadow-emerald-950/20">
                        <i class="la la-history text-sm"></i> Pesquisar
                    </button>
                    
                    @if(request('nome') || request('data_manual'))
                        <a href="{{ route('admin.painel') }}" class="bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-zinc-400 hover:text-zinc-200 p-2.5 rounded-xl transition-all flex items-center justify-center" title="Limpar Filtros">
                            <i class="la la-times-circle text-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- Conteúdo Principal --}}
    <main class="flex-grow p-4 md:p-10 max-w-6xl w-full mx-auto space-y-8">
        
        {{-- Título e Contadores Globais --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 border-b border-zinc-900 pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Controle Diário de Horários</h1>
                <p class="text-sm text-zinc-500 mt-1">Selecione o dia abaixo para visualizar as clientes agendadas.</p>
            </div>
            
            <div class="flex flex-wrap gap-3 w-full lg:w-auto justify-end">
                
                <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl px-4 py-2.5 flex items-center gap-3 min-w-[170px]">
                    <div class="bg-emerald-500/10 p-2 rounded-lg text-emerald-400">
                        <i class="la la-check-circle text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase tracking-widest text-zinc-500 font-semibold">Efetuados (Mês)</span>
                        <span class="text-lg font-black text-white leading-none">{{ $totalEfetuadosMes }}</span>
                    </div>  
                </div>

                <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl px-4 py-2.5 flex items-center gap-3 min-w-[170px]">
                    <div class="bg-pink-500/10 p-2 rounded-lg text-neon">
                        <i class="la la-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase tracking-widest text-zinc-500 font-semibold">Total (Agendados)</span>
                        <span class="text-lg font-black text-white leading-none">{{ $totalDuasSemanas }}</span>
                    </div>  
                </div>

            </div>
        </div>

        {{-- FILTRO DE DIAS OTIMIZADO --}}
        @php
            $diasSemanaPtBr = [
                'Sun' => 'Dom', 'Mon' => 'Seg', 'Tue' => 'Ter', 
                'Wed' => 'Qua', 'Thu' => 'Qui', 'Fri' => 'Sex', 'Sat' => 'Sáb'
            ];
        @endphp

        <form id="filtroDataForm" action="{{ route('admin.painel') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-7 gap-2">
                @for($i = 0; $i < 14; $i++)
                    @php
                        $dataBotao = \Carbon\Carbon::today()->addDays($i);
                        $stringData = $dataBotao->format('Y-m-d');
                        
                        $diaSemana = $diasSemanaPtBr[$dataBotao->format('D')];
                        $diaMes = $dataBotao->format('d');
                        
                        $isCheck = ($stringData === $dataSelecionada);
                        $isLotado = in_array($stringData, $diasLotados ?? []);
                        $isDomingo = $dataBotao->isSunday();
                    @endphp
                    
                    <label class="cursor-pointer {{ $isDomingo ? 'pointer-events-none' : '' }}">
                        <input type="radio" name="data_escolhida" value="{{ $stringData }}" class="hidden peer" 
                            {{ $isCheck ? 'checked' : '' }}
                            {{ $isDomingo ? 'disabled' : '' }}
                            onchange="this.form.submit();">
                        
                        <div class="flex flex-col items-center justify-center py-2.5 rounded-xl border transition-all 
                            {{ $isCheck ? 'bg-zinc-900 border-pink-500 text-neon' : 'bg-zinc-900/40 hover:border-zinc-700' }}
                            @if(!$isCheck)
                                @if($isDomingo)
                                    border-zinc-800 text-zinc-600 opacity-60
                                @elseif($isLotado)
                                    border-red-950/80 text-red-400/90 hover:bg-red-950/10
                                @else
                                    border-emerald-950/80 text-emerald-400/90 hover:bg-emerald-950/10
                                @endif
                            @endif
                        ">
                            <span class="text-[9px] uppercase tracking-wider font-semibold 
                                @if(!$isCheck)
                                    {{ $isDomingo ? 'text-zinc-600' : ($isLotado ? 'text-red-500/70' : 'text-emerald-500/70') }}
                                @endif
                            ">
                                {{ $diaSemana }}
                            </span>

                            <span class="text-base font-bold mt-0.5">{{ $diaMes }}</span>

                            <span class="text-[7px] uppercase font-bold tracking-tighter mt-0.5">
                                @if($isDomingo)
                                    Fechado
                                @elseif($isLotado)
                                    <span class="text-red-500">● Lotado</span>
                                @else
                                    <span class="text-emerald-500">● Vagas</span>
                                @endif
                            </span>
                        </div>
                    </label>
                @endfor
            </div>
        </form>
        
        {{-- 🗂️ SEÇÃO DOS CARDS --}}
        <div class="space-y-4">
            <div class="flex justify-between items-center border-b border-zinc-800 pb-2">
                <span class="text-xs uppercase tracking-widest font-semibold text-zinc-400">
                    Agendamentos para: <span class="text-zinc-200 font-bold">
                        @if(request('data_manual'))
                            {{ \Carbon\Carbon::parse(request('data_manual'))->format('d/m/Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($dataSelecionada)->format('d/m/Y') }}
                        @endif
                    </span>
                </span>
                <span class="text-xs text-zinc-500">
                    Quantidade: <strong class="text-pink-500">{{ $agendamentos->count() }}</strong>
                </span>
            </div>

            @if($agendamentos->isEmpty())
                <div class="bg-zinc-900/30 border border-zinc-800/80 rounded-2xl p-10 text-center text-zinc-500 text-sm">
                    <i class="la la-calendar-x text-3xl block mb-2 opacity-40 text-pink-500"></i>
                    Nenhum compromisso encontrado para os filtros aplicados.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($agendamentos as $agendamento)
                        <div class="card-glass p-5 rounded-2xl flex flex-col justify-between relative overflow-hidden group hover:border-zinc-700 transition-all">
                            
                            {{-- 1. Borda Lateral Colorida (Azul para Remarcado) --}}
                            <div class="absolute top-0 left-0 w-1 h-full 
                                {{ $agendamento->status === 'agendado' ? 'bg-pink-500' : '' }}
                                {{ $agendamento->status === 'remarcado' ? 'bg-blue-500' : '' }}
                                {{ $agendamento->status === 'concluido' ? 'bg-emerald-500' : '' }}
                                {{ $agendamento->status === 'nao_compareceu' ? 'bg-amber-500' : '' }}
                                {{ $agendamento->status === 'cancelado' ? 'bg-zinc-600' : '' }}
                            "></div>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-1.5 text-white font-bold text-lg bg-zinc-900/80 border border-zinc-800 px-3 py-1 rounded-xl">
                                        <i class="la la-clock text-neon"></i>
                                        {{ \Carbon\Carbon::parse($agendamento->hora_escolhida)->format('H:i') }}
                                    </div>
                                    
                                    {{-- 2. Estilo e Cor do Badge do Status --}}
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider
                                        {{ $agendamento->status === 'agendado' ? 'bg-pink-500/10 text-pink-400 border border-pink-500/20' : '' }}
                                        {{ $agendamento->status === 'remarcado' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                        {{ $agendamento->status === 'concluido' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                        {{ $agendamento->status === 'nao_compareceu' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                        {{ $agendamento->status === 'cancelado' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                                    ">
                                        {{ $agendamento->status }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-[10px] uppercase tracking-wider text-zinc-500 font-semibold">Cliente</span>
                                    <h3 class="text-base font-bold text-zinc-200">{{ $agendamento->cliente_nome }}</h3>
                                    
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $agendamento->cliente_whatsapp) }}" target="_blank" class="text-xs text-zinc-400 hover:text-green-400 inline-flex items-center gap-1 mt-1 transition">
                                        <i class="la la-whatsapp text-green-500 text-sm"></i> {{ $agendamento->cliente_whatsapp }}
                                    </a>
                                </div>

                                <div class="bg-zinc-900/40 border border-zinc-800/60 p-2.5 rounded-xl">
                                    <span class="block text-[9px] uppercase tracking-wider text-zinc-500 mb-0.5">Procedimento</span>
                                    <span class="text-xs font-semibold text-zinc-300">
                                        {{ $agendamento->servico->nome ?? 'Não especificado' }}
                                    </span>
                                </div>

                                <div class="text-xs flex justify-between items-center pt-1">
                                    <span class="text-zinc-500">Manicure:</span>
                                    <span class="font-medium text-zinc-300">{{ $agendamento->manicure->name ?? 'Não definida' }}</span>
                                </div>

                                {{-- 📜 HISTÓRICO DE REMARCAÇÃO (Audit Log) --}}
                                @if($agendamento->data_original && $agendamento->hora_original)
                                    <div class="text-[11px] bg-blue-500/10 border border-blue-500/20 text-blue-300 p-2 rounded-xl flex items-center gap-2 mt-2">
                                        <i class="la la-history text-base text-blue-400"></i>
                                        <span>
                                            <strong>Anteriormente em:</strong> 
                                            {{ \Carbon\Carbon::parse($agendamento->data_original)->format('d/m') }} 
                                            às {{ substr($agendamento->hora_original, 0, 5) }}h
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 pt-3 border-t border-zinc-900/80">
                                {{-- 4. Mantém os botões ativos tanto para 'agendado' quanto para 'remarcado' --}}
                                @if(in_array($agendamento->status, ['agendado', 'remarcado']))
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                        {{-- Botão Concluir --}}
                                        <form action="{{ route('admin.agendamento.concluir', $agendamento->id) }}" method="POST" onsubmit="confirmarPagamento(event, this)">
                                            @csrf
                                            <button type="submit" class="w-full text-center bg-emerald-600/20 hover:bg-emerald-600 border border-emerald-500/30 text-emerald-400 hover:text-white py-2 rounded-xl text-xs font-semibold transition" title="Concluir Serviço">
                                                <i class="la la-check text-base block"></i>
                                                <span class="text-[9px] uppercase tracking-tighter">Concluir</span>
                                            </button>
                                        </form>

                                        {{-- Botão Não Compareceu --}}
                                        <form action="{{ route('admin.agendamento.faltou', $agendamento->id) }}" method="POST" onsubmit="confirmarFalta(event, this)">
                                            @csrf
                                            <button type="submit" class="w-full text-center bg-amber-600/20 hover:bg-amber-600 border border-amber-500/30 text-amber-400 hover:text-white py-2 rounded-xl text-xs font-semibold transition" title="Cliente Não Compareceu">
                                                <i class="la la-user-times text-base block"></i>
                                                <span class="text-[9px] uppercase tracking-tighter">Faltou</span>
                                            </button>
                                        </form>

                                        {{-- Botão Remarcar --}}
                                        <a href="{{ route('agendamento.horarios', [
                                            'cliente_nome' => $agendamento->cliente_nome, 
                                            'cliente_whatsapp' => $agendamento->cliente_whatsapp,
                                            'servico_id' => $agendamento->servico_id,
                                            'manicure_id' => $agendamento->manicure_id,
                                            'remarcar_id' => $agendamento->id
                                        ]) }}" class="w-full text-center bg-zinc-800/80 hover:bg-zinc-700 border border-zinc-700 text-zinc-300 py-2 rounded-xl text-xs font-semibold transition flex flex-col items-center justify-center" title="Remarcar Horário">
                                            <i class="la la-calendar text-base block"></i>
                                            <span class="text-[9px] uppercase tracking-tighter">Remarcar</span>
                                        </a>

                                        {{-- Botão Cancelar --}}
                                        <form action="{{ route('admin.agendamento.cancelar', $agendamento->id) }}" method="POST" onsubmit="confirmarCancelamento(event, this)">
                                            @csrf
                                            <button type="submit" class="w-full text-center bg-red-600/10 hover:bg-red-600 border border-red-500/20 text-red-400 hover:text-white py-2 rounded-xl text-xs font-semibold transition" title="Cancelar Agendamento">
                                                <i class="la la-trash text-base block"></i>
                                                <span class="text-[9px] uppercase tracking-tighter">Cancelar</span>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    @if($agendamento->status === 'concluido')
                                        <div class="text-center text-[11px] text-emerald-400 bg-emerald-500/5 border border-emerald-500/10 rounded-xl py-1.5 flex items-center justify-center gap-1.5">
                                            <i class="la la-money-bill text-sm"></i>
                                            <span>Pago via: <strong>{{ $agendamento->forma_pagamento ?? 'Não informada' }}</strong></span>
                                        </div>
                                    @elseif($agendamento->status === 'nao_compareceu')
                                        <div class="text-center text-[11px] text-amber-400 bg-amber-500/5 border border-amber-500/10 rounded-xl py-1.5 flex items-center justify-center gap-1.5">
                                            <i class="la la-user-times text-sm"></i>
                                            <span>Não Compareceu</span>
                                        </div>
                                    @else
                                        <div class="text-center text-[11px] text-zinc-600 italic py-1">
                                            Agendamento Cancelado.
                                        </div>
                                    @endif
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-6 border-t border-zinc-900 text-center text-zinc-600 text-xs">
        <p>&copy; {{ date('Y') }} NailsStudio Design. Todos os direitos reservados.</p>
    </footer>

</body>
</html>

    {{-- JS E ALERTAS CONFIGURADOS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Máscara para formatar enquanto digita (DD/MM/AAAA)
        function mascaraData(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 8);
            if (v.length >= 5) {
                input.value = `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4)}`;
            } else if (v.length >= 3) {
                input.value = `${v.slice(0, 2)}/${v.slice(2)}`;
            } else {
                input.value = v;
            }
        }

        // Converte DD/MM/AAAA para YYYY-MM-DD antes de submeter para o servidor
        function prepararEnvioData(event) {
            const inputBr = document.getElementById('input_data_br').value;
            const hiddenInput = document.getElementById('hidden_data_manual');
            
            if (inputBr.length === 10) {
                const partes = inputBr.split('/');
                if(partes.length === 3) {
                    hiddenInput.value = `${partes[2]}-${partes[1]}-${partes[0]}`;
                }
            } else {
                hiddenInput.value = ''; // Limpa se estiver incompleto
            }
        }

        function submeterComDadosAdicionais(form, dados) {
            Object.keys(dados).forEach(key => {
                let input = form.querySelector(`input[name="${key}"]`);
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    form.appendChild(input);
                }
                input.value = dados[key];
            });
            form.submit();
        }

        function confirmarPagamento(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Concluir Atendimento',
                text: 'Preencha os dados para fechamento:',
                icon: 'info',
                html: `
                    <div class="text-left space-y-3">
                        <div>
                            <label class="block text-xs uppercase text-zinc-400 font-semibold mb-1">Forma de Pagamento</label>
                            <select id="swal-forma-pagamento" class="w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl p-2.5 text-sm focus:border-pink-500 focus:outline-none">
                                <option value="">Selecione...</option>
                                <option value="Pix">Pix</option>
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="Cartão de Débito">Cartão de Débito</option>
                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                            </select>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs uppercase text-zinc-400 font-semibold mb-1">Senha Master (Admin)</label>
                            <input type="password" id="swal-senha-admin" placeholder="Digite sua senha de login" class="w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl p-2.5 text-sm focus:border-pink-500 focus:outline-none">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                background: '#121214',
                color: '#e4e4e7',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#27272a',
                confirmButtonText: 'Confirmar e Concluir',
                cancelButtonText: 'Voltar',
                customClass: { popup: 'border border-zinc-800 rounded-3xl' },
                preConfirm: () => {
                    const formaPagamento = document.getElementById('swal-forma-pagamento').value;
                    const senhaAdmin = document.getElementById('swal-senha-admin').value;
                    if (!formaPagamento) { Swal.showValidationMessage('Você precisa selecionar uma forma de pagamento!'); return false; }
                    if (!senhaAdmin) { Swal.showValidationMessage('A senha administrativa é obrigatória!'); return false; }
                    return { formaPagamento: formaPagamento, senhaAdmin: senhaAdmin };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    submeterComDadosAdicionais(form, {
                        'forma_pagamento': result.value.formaPagamento,
                        'admin_password': result.value.senhaAdmin
                    });
                }
            });
        }

        function confirmarFalta(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Marcar como Não Compareceu?',
                text: "O horário constará como falta no sistema. Confirme com a senha master:",
                icon: 'warning',
                html: `
                    <div class="text-left mt-3">
                        <label class="block text-xs uppercase text-zinc-400 font-semibold mb-1">Senha Master (Admin)</label>
                        <input type="password" id="swal-falta-senha" placeholder="Digite sua senha de login" class="w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl p-2.5 text-sm focus:border-pink-500 focus:outline-none">
                    </div>
                `,
                showCancelButton: true,
                background: '#121214',
                color: '#e4e4e7',
                confirmButtonColor: '#f59e0b', // Cor Amber (Laranja)
                cancelButtonColor: '#27272a',
                confirmButtonText: 'Confirmar Falta',
                cancelButtonText: 'Voltar',
                customClass: { popup: 'border border-zinc-800 rounded-3xl' },
                preConfirm: () => {
                    const senhaAdmin = document.getElementById('swal-falta-senha').value;
                    if (!senhaAdmin) { Swal.showValidationMessage('A senha administrativa é obrigatória!'); return false; }
                    return { senhaAdmin: senhaAdmin };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    submeterComDadosAdicionais(form, {
                        'admin_password': result.value.senhaAdmin
                    });
                }
            });
        }

        function confirmarCancelamento(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Cancelar Agendamento?',
                text: "Esta ação liberará o horário imediatamente. Insira a senha master para confirmar:",
                icon: 'warning',
                html: `
                    <div class="text-left mt-3">
                        <label class="block text-xs uppercase text-zinc-400 font-semibold mb-1">Senha Master (Admin)</label>
                        <input type="password" id="swal-cancelar-senha" placeholder="Digite sua senha de login" class="w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl p-2.5 text-sm focus:border-pink-500 focus:outline-none">
                    </div>
                `,
                showCancelButton: true,
                background: '#121214',
                color: '#e4e4e7',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#27272a',
                confirmButtonText: 'Sim, cancelar!',
                cancelButtonText: 'Voltar',
                customClass: { popup: 'border border-zinc-800 rounded-3xl' },
                preConfirm: () => {
                    const senhaAdmin = document.getElementById('swal-cancelar-senha').value;
                    if (!senhaAdmin) { Swal.showValidationMessage('A senha administrativa é obrigatória para cancelar!'); return false; }
                    return { senhaAdmin: senhaAdmin };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    submeterComDadosAdicionais(form, {
                        'admin_password': result.value.senhaAdmin
                    });
                }
            });
        }

        @if(session('sucesso') || session('success'))
            Swal.fire({
                title: 'Sucesso!',
                text: "{{ session('sucesso') ?? session('success') }}",
                icon: 'success',
                background: '#121214',
                color: '#e4e4e7',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Ok',
                customClass: { popup: 'border border-zinc-800 rounded-3xl' }
            });
        @endif

        @if(session('erro') || session('error') || $errors->any())
            Swal.fire({
                title: 'Atenção!',
                text: "{{ session('erro') ?? session('error') ?? $errors->first() }}",
                icon: 'error',
                background: '#121214',
                color: '#e4e4e7',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Tentar Novamente',
                customClass: { popup: 'border border-zinc-800 rounded-3xl' }
            });
        @endif
    </script>
</body>
</html>