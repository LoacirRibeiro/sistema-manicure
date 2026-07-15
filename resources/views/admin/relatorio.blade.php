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
        
        /* Ajuste do calendário nativo para tema escuro */
        input[type="month"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex flex-col justify-between">

    {{-- Header --}}
    <header class="p-6 bg-zinc-950/80 border-b border-zinc-900 flex justify-between items-center backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.painel') }}" class="text-xl font-black tracking-tighter uppercase">Nails<span class="text-neon">Studio</span></a>
            <span class="text-xs uppercase tracking-wider bg-zinc-900 px-3 py-1 rounded-full text-zinc-400 border border-zinc-800">Relatórios</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.painel') }}" class="text-xs font-semibold uppercase tracking-wider border border-zinc-800 bg-zinc-900/50 text-zinc-300 px-4 py-2.5 rounded-xl transition-all duration-300 hover:bg-zinc-800 hover:text-white flex items-center gap-1.5">
                <i class="la la-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </header>

    {{-- Conteúdo Principal --}}
    <main class="flex-grow p-4 md:p-10 max-w-6xl w-full mx-auto space-y-8">
        
        {{-- Filtro Mensal Totalmente em Português (Sem inputs nativos do navegador) --}}
        <section class="card-glass p-6 rounded-3xl border border-zinc-800/80 bg-zinc-900/10">
            <form action="{{ route('admin.relatorio') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4 w-full">
                
                {{-- Container dos Seletores --}}
                <div class="grid grid-cols-2 gap-3 flex-grow w-full">
                    
                    {{-- Seletor de Mês --}}
                    <div class="space-y-1.5">
                        <label for="filtro_mes" class="block text-xs uppercase tracking-wider text-zinc-400 font-semibold">Mês</label>
                        <div class="relative">
                            <i class="la la-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-base z-10"></i>
                            <select id="filtro_mes" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl pl-9 pr-4 py-2.5 text-sm focus:border-pink-500 focus:outline-none transition-all appearance-none cursor-pointer">
                                @php
                                    // Extrai o mês do período atual (ex: de "2026-07" pega "07")
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

                    {{-- Seletor de Ano --}}
                    <div class="space-y-1.5">
                        <label for="filtro_ano" class="block text-xs uppercase tracking-wider text-zinc-400 font-semibold">Ano</label>
                        <div class="relative">
                            <i class="la la-history absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-base z-10"></i>
                            <select id="filtro_ano" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl pl-9 pr-4 py-2.5 text-sm focus:border-pink-500 focus:outline-none transition-all appearance-none cursor-pointer">
                                @php
                                    // Extrai o ano do período atual (ex: de "2026-07" pega "2026")
                                    $anoAtual = request('periodo') ? explode('-', request('periodo'))[0] : date('Y');
                                    $anoInicio = 2024; // Ano de início do seu sistema
                                    $anoFim = date('Y') + 1; // Ano atual + 1 de margem
                                @endphp
                                @for($ano = $anoInicio; $ano <= $anoFim; $ano++)
                                    <option value="{{ $ano }}" {{ $anoAtual == $ano ? 'selected' : '' }}>{{ $ano }}</option>
                                @endfor
                            </select>
                            <i class="la la-angle-down absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none"></i>
                        </div>
                    </div>

                </div>

                {{-- Campo oculto (hidden) que o Laravel continuará recebendo no formato "YYYY-MM" --}}
                <input type="hidden" id="hidden_periodo" name="periodo" value="{{ $periodoSelecionado }}">

                {{-- Botão Filtrar --}}
                <button type="submit" onclick="atualizarPeriodoOculto()" class="w-full md:w-auto bg-pink-500/10 hover:bg-neon border border-pink-500/30 text-neon hover:text-white px-6 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-1.5 whitespace-nowrap h-11">
                    <i class="la la-filter text-base"></i> Filtrar Mês
                </button>
            </form>
        </section>

        {{-- Cards de Contagem / Métricas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Concluídos / Atendidos --}}
            <div class="card-glass p-5 rounded-2xl border-l-4 border-emerald-500">
                <span class="block text-[10px] uppercase tracking-wider text-zinc-500 font-semibold">Concluídos</span>
                <span class="text-2xl font-black text-white block mt-1">{{ $contagem['concluidos'] }}</span>
                <span class="text-[10px] text-emerald-400 mt-1 block">Atendimentos bem-sucedidos</span>
            </div>

            {{-- Faltaram (Não Compareceram) --}}
            <div class="card-glass p-5 rounded-2xl border-l-4 border-amber-500">
                <span class="block text-[10px] uppercase tracking-wider text-zinc-500 font-semibold">Não Compareceram</span>
                <span class="text-2xl font-black text-white block mt-1">{{ $contagem['nao_compareceu'] }}</span>
                <span class="text-[10px] text-amber-400 mt-1 block">Faltas registradas</span>
            </div>

            {{-- Remarcados --}}
            <div class="card-glass p-5 rounded-2xl border-l-4 border-blue-500">
                <span class="block text-[10px] uppercase tracking-wider text-zinc-500 font-semibold">Remarcados</span>
                <span class="text-2xl font-black text-white block mt-1">{{ $contagem['remarcados'] }}</span>
                <span class="text-[10px] text-blue-400 mt-1 block">Horários remarcados</span>
            </div>

            {{-- Cancelados --}}
            <div class="card-glass p-5 rounded-2xl border-l-4 border-red-500">
                <span class="block text-[10px] uppercase tracking-wider text-zinc-500 font-semibold">Cancelados</span>
                <span class="text-2xl font-black text-white block mt-1">{{ $contagem['cancelados'] }}</span>
                <span class="text-[10px] text-red-400 mt-1 block">Agendamentos suspensos</span>
            </div>
        </div>

        {{-- Tabela de Detalhes dos Agendamentos --}}
        <div class="card-glass rounded-3xl border border-zinc-800/80 overflow-hidden">
            <div class="p-6 border-b border-zinc-800 flex justify-between items-center bg-zinc-950/20">
                <h3 class="font-bold text-base text-white">Detalhamento do Período</h3>
                <span class="text-xs text-zinc-500">Registros encontrados: <strong class="text-zinc-300">{{ $agendamentos->count() }}</strong></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-800 text-[10px] uppercase tracking-wider text-zinc-500 bg-zinc-950/40">
                            <th class="p-4 font-semibold">Data/Hora</th>
                            <th class="p-4 font-semibold">Cliente</th>
                            <th class="p-4 font-semibold">Procedimento</th>
                            <th class="p-4 font-semibold">Manicure</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Forma de Pagamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900">
                        @if($agendamentos->isEmpty())
                            <tr>
                                <td colspan="6" class="p-10 text-center text-zinc-500 text-sm">
                                    Nenhum registro encontrado para o mês selecionado.
                                </td>
                            </tr>
                        @else
                            @foreach($agendamentos as $agendamento)
                                <tr class="hover:bg-zinc-900/20 transition-all">
                                    <td class="p-4 text-xs font-medium whitespace-nowrap">
                                        <span class="block text-white font-bold">{{ \Carbon\Carbon::parse($agendamento->data_escolhida)->format('d/m/Y') }}</span>
                                        <span class="text-[10px] text-zinc-500">{{ \Carbon\Carbon::parse($agendamento->hora_escolhida)->format('H:i') }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="block text-sm font-bold text-zinc-200">{{ $agendamento->cliente_nome }}</span>
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $agendamento->cliente_whatsapp) }}" target="_blank" class="text-[11px] text-zinc-400 hover:text-green-400 inline-flex items-center gap-1 transition">
                                            <i class="la la-whatsapp text-green-500"></i> {{ $agendamento->cliente_whatsapp }}
                                        </a>
                                    </td>
                                    <td class="p-4 text-xs text-zinc-300">
                                        {{ $agendamento->servico->nome ?? 'Não especificado' }}
                                    </td>
                                    <td class="p-4 text-xs text-zinc-400">
                                        {{ $agendamento->manicure->name ?? 'Não definida' }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider
                                            {{ $agendamento->status === 'confirmado' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                            {{ $agendamento->status === 'concluido' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                            {{ $agendamento->status === 'nao_compareceu' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                            {{ $agendamento->status === 'cancelado' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                                        ">
                                            {{ $agendamento->status === 'nao_compareceu' ? 'Faltou' : $agendamento->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs text-zinc-300 font-semibold">
                                        @if($agendamento->status === 'concluido')
                                            <span class="flex items-center gap-1 text-emerald-400">
                                                <i class="la la-money-bill text-sm"></i> {{ $agendamento->forma_pagamento ?? 'Não informada' }}
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
        // Restaura dinamicamente o tipo de input para 'month' caso já venha preenchido do backend
        document.addEventListener('DOMContentLoaded', function() {
            const inputMes = document.getElementById('mes_ano');
            if(inputMes.value) {
                inputMes.type = 'month';
            }
        });

        function atualizarPeriodoOculto() {
            const mes = document.getElementById('filtro_mes').value;
            const ano = document.getElementById('filtro_ano').value;
            // Junta no formato YYYY-MM esperado pelo seu Controller
            document.getElementById('hidden_periodo').value = `${ano}-${mes}`;
        }
    </script>
</body>
</html>