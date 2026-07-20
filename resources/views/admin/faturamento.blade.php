<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faturamento - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.3); }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex flex-col justify-between">

    {{-- Header --}}
    <header class="p-4 md:p-6 bg-zinc-950/80 border-b border-zinc-900 flex justify-between items-center backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.painel') }}" class="text-lg md:text-xl font-black tracking-tighter uppercase whitespace-nowrap">
                Nails<span class="text-neon">Studio</span>
            </a>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.painel') }}" class="text-xs font-semibold uppercase tracking-wider border border-pink-500/30 bg-pink-500/10 text-neon px-4 py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-neon hover:text-white hover:shadow-[0_0_15px_rgba(255,0,127,0.5)] flex items-center gap-1.5 whitespace-nowrap">
                <span class="hidden sm:inline">Painel</span>
                <span class="sm:hidden">Voltar</span>
            </a>
        </div>
    </header>

    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-3">
        <div class="flex justify-start mt-4 ml-4">
            <span class="text-xs uppercase tracking-wider bg-red-950/50 px-3 py-1 rounded-full text-red-400 border border-red-900/50">
                Financeiro
            </span>
        </div>
    </div>

    <main class="flex-grow p-4 md:p-10 max-w-6xl w-full mx-auto space-y-6">
        
        {{-- Card de Filtro e Título --}}
        <div class="w-full">
            <form action="{{ route('admin.faturamento') }}" method="GET" class="w-full card-glass p-5 md:p-6 rounded-3xl border border-zinc-800/80 bg-zinc-900/10 flex flex-col gap-5 shadow-xl">
                
                {{-- Cabeçalho do Filtro com Badge e Botão Lançar Despesa --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-zinc-800/60 pb-4">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white">Análise de Faturamento</h1>
                        </div>
                        <p class="text-xs sm:text-sm text-zinc-400 mt-1">Balanço consolidado com base em atendimentos concluídos e despesas.</p>
                    </div>

                    {{-- Botão Lançar Despesa --}}
                    <a href="{{ route('admin.despesas.index') }}" class="w-full sm:w-auto text-xs font-semibold uppercase tracking-wider border border-pink-500/30 bg-pink-500/10 text-neon hover:text-white hover:bg-neon px-5 py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-[0_0_15px_rgba(255,0,127,0.5)] flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <i class="la la-plus text-base"></i> Lançar Despesa
                    </a>
                </div>

                {{-- Campos do Filtro --}}
                <div class="w-full flex flex-col md:flex-row items-stretch md:items-end gap-4">
                    @php
                        $partes = explode('-', $mesAno);
                        $anoAtual = $partes[0] ?? date('Y');
                        $mesAtual = $partes[1] ?? date('m');

                        $meses = [
                            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', 
                            '04' => 'Abril',   '05' => 'Maio',      '06' => 'Junho', 
                            '07' => 'Julho',   '08' => 'Agosto',    '09' => 'Setembro', 
                            '10' => 'Outubro', '11' => 'Novembro',  '12' => 'Dezembro'
                        ];
                    @endphp

                    <input type="hidden" id="mesAnoOculto" name="mes" value="{{ $mesAno }}">

                    {{-- Select de Mês --}}
                    <div class="w-full md:flex-1">
                        <label for="selectMes" class="block text-[10px] uppercase tracking-widest text-zinc-400 font-semibold mb-1.5">Mês de Análise</label>
                        <select id="selectMes" onchange="atualizarFiltro()" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 px-4 py-3 rounded-xl text-sm focus:outline-none focus:border-pink-500/50 transition-all cursor-pointer">
                            @foreach($meses as $numero => $nome)
                                <option value="{{ $numero }}" {{ $mesAtual == $numero ? 'selected' : '' }}>
                                    {{ $nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Select de Ano --}}
                    <div class="w-full md:w-48">
                        <label for="selectAno" class="block text-[10px] uppercase tracking-widest text-zinc-400 font-semibold mb-1.5">Ano</label>
                        <select id="selectAno" onchange="atualizarFiltro()" class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 px-4 py-3 rounded-xl text-sm focus:outline-none focus:border-pink-500/50 transition-all cursor-pointer">
                            @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}" {{ $anoAtual == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                </div>

            </form>
        </div>

        {{-- Bloco Superior: Balanço Consolidado --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- Card 1: Faturamento Bruto --}}
            <div class="card-glass p-6 rounded-3xl border-l-4 border-emerald-500 flex justify-between items-center shadow-lg">
                <div>
                    <span class="block text-xs uppercase tracking-widest text-zinc-400 font-semibold">Faturamento Bruto</span>
                    <span class="text-2xl font-black text-white mt-1 block">R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
                </div>
                <div class="bg-emerald-500/10 p-3 rounded-xl text-emerald-400 text-2xl">
                    <i class="la la-arrow-up"></i>
                </div>
            </div>

            {{-- Card 2: Despesas Totais --}}
            <div class="card-glass p-6 rounded-3xl border-l-4 border-red-500 flex justify-between items-center shadow-lg">
                <div>
                    <span class="block text-xs uppercase tracking-widest text-zinc-400 font-semibold">Despesas Totais</span>
                    <span class="text-2xl font-black text-white mt-1 block">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</span>
                </div>
                <div class="bg-red-500/10 p-3 rounded-xl text-red-400 text-2xl">
                    <i class="la la-arrow-down"></i>
                </div>
            </div>

            {{-- Card 3: Lucro Líquido --}}
            <div class="card-glass p-6 rounded-3xl border-l-4 {{ $lucroLiquido >= 0 ? 'border-pink-500' : 'border-amber-600' }} flex justify-between items-center shadow-lg">
                <div>
                    <span class="block text-xs uppercase tracking-widest text-zinc-400 font-semibold">Lucro Líquido</span>
                    <span class="text-2xl font-black {{ $lucroLiquido >= 0 ? 'text-neon' : 'text-amber-500' }} mt-1 block">
                        R$ {{ number_format($lucroLiquido, 2, ',', '.') }}
                    </span>
                </div>
                <div class="p-3 rounded-xl text-2xl {{ $lucroLiquido >= 0 ? 'bg-pink-500/10 text-neon' : 'bg-amber-500/10 text-amber-500' }}">
                    <i class="la la-wallet"></i>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Card: Por Forma de Pagamento --}}
            <div class="card-glass p-6 rounded-3xl space-y-4 shadow-lg">
                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-400 border-b border-zinc-800/60 pb-2 flex items-center gap-2">
                    <i class="la la-money-check text-neon text-lg"></i> Meios de Pagamento
                </h3>
                <div class="space-y-3">
                    @forelse($porFormaPagamento as $forma)
                        <div class="flex justify-between items-center bg-zinc-900/40 p-3 rounded-xl border border-zinc-800/60">
                            <span class="text-sm font-medium text-zinc-300">{{ $forma->forma_pagamento }}</span>
                            <span class="text-sm font-bold text-white">R$ {{ number_format($forma->total, 2, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-zinc-500 text-xs italic">Nenhum registro encontrado.</p>
                    @endforelse
                </div>
            </div>

            {{-- Card: Por Manicure --}}
            <div class="card-glass p-6 rounded-3xl space-y-4 shadow-lg">
                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-400 border-b border-zinc-800/60 pb-2 flex items-center gap-2">
                    <i class="la la-user-friends text-neon text-lg"></i> Desempenho por Profissional
                </h3>
                <div class="space-y-3">
                    @forelse($porManicure as $m)
                        <div class="flex justify-between items-center bg-zinc-900/40 p-3 rounded-xl border border-zinc-800/60">
                            <div>
                                <span class="text-sm font-medium text-zinc-300 block">{{ $m->manicure }}</span>
                                <span class="text-[10px] text-zinc-500">{{ $m->qtd }} atendimentos</span>
                            </div>
                            <span class="text-sm font-bold text-emerald-400">R$ {{ number_format($m->total, 2, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-zinc-500 text-xs italic">Nenhum registro encontrado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Card Inferior: Histórico Diário --}}
        <div class="card-glass p-6 rounded-3xl space-y-4 shadow-lg">
            <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-400 border-b border-zinc-800/60 pb-2 flex items-center gap-2">
                <i class="la la-chart-bar text-neon text-lg"></i> Evolução Diária do Mês
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-400">
                    <thead class="text-xs uppercase text-zinc-500 bg-zinc-900/60">
                        <tr>
                            <th class="p-3 rounded-l-xl">Dia</th>
                            <th class="p-3">Atendimentos</th>
                            <th class="p-3 rounded-r-xl text-right">Total Gerado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60">
                        @forelse($faturamentoDiario as $diario)
                            <tr class="hover:bg-zinc-900/40 transition-all">
                                <td class="p-3 font-semibold text-zinc-300">{{ \Carbon\Carbon::parse($diario->data_escolhida)->format('d/m/Y') }}</td>
                                <td class="p-3"><span class="bg-zinc-800/80 px-2.5 py-1 rounded-lg text-xs text-zinc-300 font-medium">{{ $diario->qtd }}</span></td>
                                <td class="p-3 text-right font-bold text-white">R$ {{ number_format($diario->total, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-zinc-600 italic">Sem movimentações registradas neste mês.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="py-6 border-t border-zinc-900 text-center text-zinc-600 text-xs">
        <p>&copy; {{ date('Y') }} NailsStudio Design. Todos os direitos reservados.</p>
    </footer>

    <script>
        function atualizarFiltro() {
            const mes = document.getElementById('selectMes').value;
            const ano = document.getElementById('selectAno').value;
            const inputOculto = document.getElementById('mesAnoOculto');
            
            inputOculto.value = `${ano}-${mes}`;
            inputOculto.form.submit();
        }
    </script>
</body>
</html>