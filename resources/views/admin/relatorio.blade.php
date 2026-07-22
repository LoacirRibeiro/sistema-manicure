<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Mensal de Agendamentos - NailsStudio</title>
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
            <a href="{{ route('admin.painel') }}" class="text-lg md:text-xl font-black tracking-tighter uppercase whitespace-nowrap">
                Nails<span class="text-neon">Studio</span>
            </a>
        </div>

        {{-- Ação Principal --}}
        <div class="flex items-center gap-2 sm:gap-4">
            <a href="{{ route('admin.painel') }}" class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider border border-pink-500/30 bg-pink-500/10 text-neon px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-neon hover:text-white hover:shadow-[0_0_15px_rgba(255,0,127,0.5)] flex items-center justify-center gap-1.5 whitespace-nowrap">
                <i class="la la-arrow-left text-sm"></i>
                <span>Voltar ao Painel</span>
            </a>
        </div>
    </header>

    {{-- Faixa Secundária de Título --}}
    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-3">
        <div class="flex justify-start mt-4 ml-4 md:ml-10">
            <span class="text-xs uppercase tracking-wider bg-pink-950/50 px-3 py-1 rounded-full text-pink-400 border border-pink-900/50 font-semibold">
                Relatórios Mensais
            </span>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    <main class="flex-grow p-4 md:p-10 max-w-6xl w-full mx-auto space-y-8">

        {{-- Título e Filtro de Mês/Ano --}}
        <div class="space-y-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Relatório de Agendamentos</h1>
                <p class="text-sm text-zinc-500 mt-1">Filtre por período para analisar a taxa de conclusão, cancelamentos e faltas.</p>
            </div>

            {{-- Formulário de Filtro Mensal --}}
            <form id="form_filtro" action="{{ route('admin.relatorio') }}" method="GET" class="card-glass p-5 rounded-2xl grid grid-cols-1 md:grid-cols-3 gap-4 items-end border border-zinc-800/60">
                
                {{-- Mês --}}
                <div class="space-y-1.5">
                    <label for="filtro_mes" class="block text-xs uppercase tracking-wider text-zinc-400 font-semibold">Mês de Referência</label>
                    <div class="relative">
                        <i class="la la-calendar absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-base pointer-events-none"></i>
                        <select id="filtro_mes" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl pl-9 pr-8 py-2.5 text-sm focus:border-pink-500 focus:outline-none transition-all appearance-none cursor-pointer">
                            @php
                                $mesAtual = request('periodo') ? explode('-', request('periodo'))[1] : date('m');
                                $meses = [
                                    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                                    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                                    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                                ];
                            @endphp
                            @foreach($meses as $num => $nome)
                                <option value="{{ $num }}" {{ $mesAtual == $num ? 'selected' : '' }}>{{ $nome }}</option>
                            @endforeach
                        </select>
                        <i class="la la-angle-down absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none"></i>
                    </div>
                </div>

                {{-- Ano --}}
                <div class="space-y-1.5">
                    <label for="filtro_ano" class="block text-xs uppercase tracking-wider text-zinc-400 font-semibold">Ano</label>
                    <div class="relative">
                        <i class="la la-history absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-base pointer-events-none"></i>
                        <select id="filtro_ano" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl pl-9 pr-8 py-2.5 text-sm focus:border-pink-500 focus:outline-none transition-all appearance-none cursor-pointer">
                            @php
                                $anoAtual = request('periodo') ? explode('-', request('periodo'))[0] : date('Y');
                                $anoInicio = 2024;
                                $anoFim = date('Y') + 1;
                            @endphp
                            @for($ano = $anoInicio; $ano <= $anoFim; $ano++)
                                <option value="{{ $ano }}" {{ $anoAtual == $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endfor
                        </select>
                        <i class="la la-angle-down absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none"></i>
                    </div>
                </div>

                <input type="hidden" id="hidden_periodo" name="periodo" value="{{ $periodoSelecionado ?? date('Y-m') }}">

                {{-- Botão Filtrar --}}
                <button type="submit" onclick="atualizarPeriodoOculto()" class="w-full bg-emerald-600 hover:bg-emerald-700 border border-emerald-500 text-white font-bold py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-lg shadow-emerald-950/20">
                    <i class="la la-filter text-base"></i> Filtrar Período
                </button>
            </form>
        </div>

        {{-- Cards de Métricas (Padronizados com os Cards de Indicadores do Dashboard) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            
            {{-- Concluídos --}}
            <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl p-4 flex items-center gap-3">
                <div class="bg-emerald-500/10 p-2.5 rounded-xl text-emerald-400 shrink-0">
                    <i class="la la-check-circle text-2xl"></i>
                </div>
                <div>
                    <span class="block text-[10px] uppercase tracking-widest text-zinc-500 font-semibold">Concluídos</span>
                    <span class="text-xl font-black text-white leading-tight block mt-0.5">{{ $contagem['concluidos'] ?? 0 }}</span>
                </div>
            </div>

            {{-- Faltou --}}
            <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl p-4 flex items-center gap-3">
                <div class="bg-amber-500/10 p-2.5 rounded-xl text-amber-400 shrink-0">
                    <i class="la la-user-times text-2xl"></i>
                </div>
                <div>
                    <span class="block text-[10px] uppercase tracking-widest text-zinc-500 font-semibold">Faltou</span>
                    <span class="text-xl font-black text-white leading-tight block mt-0.5">{{ $contagem['nao_compareceu'] ?? 0 }}</span>
                </div>
            </div>

            {{-- Remarcados --}}
            <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl p-4 flex items-center gap-3">
                <div class="bg-blue-500/10 p-2.5 rounded-xl text-blue-400 shrink-0">
                    <i class="la la-clock text-2xl"></i>
                </div>
                <div>
                    <span class="block text-[10px] uppercase tracking-widest text-zinc-500 font-semibold">Remarcados</span>
                    <span class="text-xl font-black text-white leading-tight block mt-0.5">{{ $contagem['remarcados'] ?? 0 }}</span>
                </div>
            </div>

            {{-- Cancelados --}}
            <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl p-4 flex items-center gap-3">
                <div class="bg-red-500/10 p-2.5 rounded-xl text-red-400 shrink-0">
                    <i class="la la-times-circle text-2xl"></i>
                </div>
                <div>
                    <span class="block text-[10px] uppercase tracking-widest text-zinc-500 font-semibold">Cancelados</span>
                    <span class="text-xl font-black text-white leading-tight block mt-0.5">{{ $contagem['cancelados'] ?? 0 }}</span>
                </div>
            </div>

        </div>

        {{-- Tabela de Detalhes dos Agendamentos --}}
        <div class="card-glass rounded-2xl border border-zinc-800/80 overflow-hidden">
            <div class="p-4 md:p-5 border-b border-zinc-800/80 flex justify-between items-center bg-zinc-950/40">
                <span class="text-xs uppercase tracking-wider font-bold text-zinc-300 flex items-center gap-2">
                    <i class="la la-list text-neon text-base"></i> Detalhamento do Mês
                </span>
                <span class="text-xs text-zinc-500">
                    Registros: <strong class="text-pink-500 font-bold">{{ $agendamentos->count() }}</strong>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-800/80 text-[10px] uppercase tracking-wider text-zinc-500 bg-zinc-950/60">
                            <th class="p-4 font-semibold">Data / Hora</th>
                            <th class="p-4 font-semibold">Cliente</th>
                            <th class="p-4 font-semibold">Procedimento</th>
                            <th class="p-4 font-semibold">Manicure</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Pagamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900/80 text-sm">
                        @if($agendamentos->isEmpty())
                            <tr>
                                <td colspan="6" class="p-10 text-center text-zinc-500 text-xs">
                                    <i class="la la-calendar-x text-3xl block mb-2 opacity-40 text-pink-500"></i>
                                    Nenhum agendamento encontrado para o mês selecionado.
                                </td>
                            </tr>
                        @else
                            @foreach($agendamentos as $agendamento)
                                <tr class="hover:bg-zinc-900/30 transition-all">
                                    <td class="p-4 text-xs font-medium whitespace-nowrap">
                                        <span class="block text-zinc-200 font-bold">{{ \Carbon\Carbon::parse($agendamento->data_escolhida)->format('d/m/Y') }}</span>
                                        <span class="text-[11px] text-zinc-500 flex items-center gap-1 mt-0.5">
                                            <i class="la la-clock text-pink-500"></i> {{ \Carbon\Carbon::parse($agendamento->hora_escolhida)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span class="block text-xs font-bold text-zinc-200">{{ $agendamento->cliente_nome }}</span>
                                        <a href="https://wa.me/55{{ preg_replace('/\D/', '', $agendamento->cliente_whatsapp) }}" target="_blank" class="text-[11px] text-zinc-400 hover:text-green-400 inline-flex items-center gap-1 transition mt-0.5">
                                            <i class="la la-whatsapp text-green-500"></i> {{ $agendamento->cliente_whatsapp }}
                                        </a>
                                    </td>
                                    <td class="p-4 text-xs text-zinc-300 font-medium">
                                        {{ $agendamento->servico->nome ?? 'Não especificado' }}
                                    </td>
                                    <td class="p-4 text-xs text-zinc-400">
                                        {{ $agendamento->manicure->name ?? 'Não definida' }}
                                    </td>
                                    
                                    {{-- Status Formatado em Badge --}}
                                    <td class="p-4 whitespace-nowrap">
                                        @php
                                            $statusSlug = strtolower(trim($agendamento->status));
                                            $isRemarcado = $agendamento->is_remarcado || $statusSlug === 'remarcado';
                                        @endphp

                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider inline-block
                                            {{ $isRemarcado ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                            {{ !$isRemarcado && in_array($statusSlug, ['agendado', 'confirmado']) ? 'bg-pink-500/10 text-pink-400 border border-pink-500/20' : '' }}
                                            {{ $statusSlug === 'concluido' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                            {{ $statusSlug === 'nao_compareceu' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                            {{ $statusSlug === 'cancelado' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                                        ">
                                            @if($isRemarcado)
                                                Remarcado
                                            @elseif($statusSlug === 'nao_compareceu')
                                                Faltou
                                            @elseif(in_array($statusSlug, ['agendado', 'confirmado']))
                                                Agendado
                                            @else
                                                {{ ucfirst($agendamento->status) }}
                                            @endif
                                        </span>
                                    </td>

                                    <td class="p-4 text-xs font-medium">
                                        @if($agendamento->status === 'concluido')
                                            <span class="inline-flex items-center gap-1.5 text-emerald-400 bg-emerald-500/5 px-2.5 py-1 rounded-lg border border-emerald-500/10">
                                                <i class="la la-wallet text-sm"></i> {{ $agendamento->forma_pagamento ?? 'Não informada' }}
                                            </span>
                                        @else
                                            <span class="text-zinc-600">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    {{-- Footer --}}
    <footer class="py-6 border-t border-zinc-900 text-center text-zinc-600 text-xs">
        <p>&copy; {{ date('Y') }} NailsStudio Design. Todos os direitos reservados.</p>
    </footer>

    <script>
        function atualizarPeriodoOculto() {
            const mes = document.getElementById('filtro_mes').value;
            const ano = document.getElementById('filtro_ano').value;
            document.getElementById('hidden_periodo').value = `${ano}-${mes}`;
        }
    </script>
</body>
</html>