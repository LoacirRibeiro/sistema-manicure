<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desempenho Mensal - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght=300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.3); }
        .bg-neon { background-color: #FF007F; }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex flex-col justify-between">

    {{-- Header --}}
    <header class="p-6 bg-zinc-950/80 border-b border-zinc-900 flex justify-between items-center backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <span class="text-xl font-black tracking-tighter uppercase">Nails<span class="text-neon">Studio</span></span>
        </div>
        <div>
            <a href="{{ route('admin.painel') }}" class="w-full md:w-auto bg-pink-500/10 hover:bg-neon border border-pink-500/30 text-neon hover:text-white px-6 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-1.5 whitespace-nowrap h-11">
                Painel
            </a>
        </div>
    </header>

    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-3">
        <div class="flex justify-start mt-4 ml-4">
            <span class="text-xs uppercase tracking-wider bg-red-950/50 px-3 py-1 rounded-full text-red-400 border border-red-900/50">
                Gráficos & Análise
            </span>
        </div>
    </div>

    <div class="w-full max-w-6xl mx-auto mt-6 mb-8 px-4 sm:px-">
        <form action="{{ route('admin.graficos') }}" method="GET" class="card-glass p-5 md:p-6 rounded-3xl border border-zinc-800/80 bg-zinc-900/10 flex flex-col gap-5 shadow-xl">

            {{-- Campos do Filtro --}}
            <div class="flex flex-col sm:flex-row items-end gap-4">
                
                {{-- Mês --}}
                <div class="w-full sm:flex-1">
                    <label for="filtro_mes" class="block text-[10px] uppercase tracking-widest text-zinc-400 font-semibold mb-1.5">Mês de Análise</label>
                    <select 
                        id="filtro_mes" 
                        name="mes" 
                        class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 px-4 py-2.5 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-pink-500/50 transition-all cursor-pointer"
                    >
                        @php
                            $meses = [
                                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                                '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                                '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                            ];
                            $mesSelecionado = request('mes', date('m'));
                        @endphp
                        @foreach($meses as $num => $nome)
                            <option value="{{ $num }}" {{ $mesSelecionado == $num ? 'selected' : '' }}>
                                {{ $nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Ano --}}
                <div class="w-full sm:w-1/3">
                    <label for="filtro_ano" class="block text-[10px] uppercase tracking-widest text-zinc-400 font-semibold mb-1.5">Ano</label>
                    <select 
                        id="filtro_ano" 
                        name="ano" 
                        class="w-full bg-zinc-950 border border-zinc-800 text-zinc-300 px-4 py-2.5 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-pink-500/50 transition-all cursor-pointer"
                    >
                        @php
                            $anoAtual = date('Y');
                            $anoSelecionado = request('ano', $anoAtual);
                        @endphp
                        @for($i = $anoAtual; $i >= $anoAtual - 3; $i--)
                            <option value="{{ $i }}" {{ $anoSelecionado == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Botões de Ação --}}
                <div class="flex items-center gap-2 w-full sm:w-auto pt-2 sm:pt-0">
                    <button type="submit" class="w-full sm:w-auto bg-pink-500/10 hover:bg-neon border border-pink-500/30 text-neon hover:text-white px-5 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-300 flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <i class="la la-filter text-base"></i> Filtrar
                    </button>
                    
                    @if(request('mes') || request('ano'))
                        <a href="{{ route('admin.graficos') }}" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-300 flex items-center justify-center whitespace-nowrap" title="Limpar Filtro">
                            <i class="la la-times text-base sm:mr-1"></i>
                            <span class="hidden sm:inline">Limpar</span>
                        </a>
                    @endif
                </div>

            </div>

        </form>
    </div>

    {{-- Conteúdo Principal --}}
    <main class="flex-grow p-4 md:p-10 max-w-6xl w-full mx-auto space-y-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 border-b border-zinc-900 pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Desempenho Mensal</h1>
                <p class="text-sm text-zinc-500 mt-1">Análise de agendamentos realizados em {{ \Carbon\Carbon::now()->translatedFormat('F \d\e Y') }}</p>
            </div>
        </div>

        {{-- Cards Rápidos --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-glass p-5 rounded-2xl border-l-4 border-emerald-500">
                <span class="text-xs text-zinc-500 uppercase font-semibold">Concluídos</span>
                <h3 class="text-2xl font-black text-white mt-1">{{ $metricas['concluido'] }}</h3>
            </div>
            <div class="card-glass p-5 rounded-2xl border-l-4 border-red-500">
                <span class="text-xs text-zinc-500 uppercase font-semibold">Cancelados</span>
                <h3 class="text-2xl font-black text-white mt-1">{{ $metricas['cancelado'] }}</h3>
            </div>
            <div class="card-glass p-5 rounded-2xl border-l-4 border-amber-500">
                <span class="text-xs text-zinc-500 uppercase font-semibold">Faltas (N/C)</span>
                <h3 class="text-2xl font-black text-white mt-1">{{ $metricas['nao_compareceu'] }}</h3>
            </div>
            <div class="card-glass p-5 rounded-2xl border-l-4 border-cyan-500">
                <span class="text-xs text-zinc-500 uppercase font-semibold">Remarcados</span>
                <h3 class="text-2xl font-black text-white mt-1">{{ $metricas['remarcado'] }}</h3>
            </div>
        </div>

        {{-- Seção de Gráficos Unificados em Barras --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Gráfico 1: Volume por Categoria --}}
            <div class="card-glass p-6 rounded-3xl lg:col-span-6 flex flex-col justify-between min-h-[400px]">
                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-400 mb-4">Volume por Categoria</h3>
                <div class="relative flex-grow flex items-center justify-center">
                    <canvas id="chartBarras"></canvas>
                </div>
            </div>

            {{-- Gráfico 2: Saúde Financeira (Agora também em Barras) --}}
            <div class="card-glass p-6 rounded-3xl lg:col-span-6 flex flex-col justify-between min-h-[400px]">
                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-400 mb-4">Saúde Financeira (Faturamento vs. Despesas)</h3>
                <div class="relative flex-grow flex items-center justify-center">
                    <canvas id="chartFinanceiroBarras"></canvas>
                </div>
            </div>

        </div>

    </main>

    {{-- Footer --}}
    <footer class="py-6 border-t border-zinc-900 text-center text-zinc-600 text-xs">
        <p>&copy; {{ date('Y') }} NailsStudio Design. Todos os direitos reservados.</p>
    </footer>

    {{-- Geração dos Gráficos via JS --}}
    <script>
        // Dados vindos do Controller do Laravel
        const dadosGraficos = {
            concluidos: {{ $metricas['concluido'] }},
            cancelados: {{ $metricas['cancelado'] }},
            faltas: {{ $metricas['nao_compareceu'] }},
            remarcados: {{ $metricas['remarcado'] }}
        };

        const coresStatus = {
            concluidos: '#10b981', // Emerald 500
            cancelados: '#ef4444',  // Red 500
            faltas: '#f59e0b',      // Amber 500
            remarcados: '#06b6d4'   // Cyan 500
        };

        // Configuração Global de Cores das fontes do Chart.js para combinar com o modo escuro
        Chart.defaults.color = '#71717a'; 
        Chart.defaults.font.family = 'Poppins';

        // 1. Inicializando Gráfico de Barras - Status
        const ctxBarras = document.getElementById('chartBarras').getContext('2d');
        new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: ['Concluídos', 'Cancelados', 'Faltas', 'Remarcados'],
                datasets: [{
                    label: 'Atendimentos',
                    data: [dadosGraficos.concluidos, dadosGraficos.cancelados, dadosGraficos.faltas, dadosGraficos.remarcados],
                    backgroundColor: [coresStatus.concluidos, coresStatus.cancelados, coresStatus.faltas, coresStatus.remarcados],
                    borderRadius: 8,
                    borderWidth: 0,
                    barThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { stepSize: 1, color: '#a1a1aa' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#a1a1aa' }
                    }
                }
            }
        });

        // Dados Financeiros vindos do Controller
        const financeiro = {
            faturamento: {{ $faturamentoTotal ?? 0 }},
            despesas: {{ $despesasTotal ?? 0 }},
            lucro: {{ $lucroLiquido ?? 0 }}
        };

        // 2. Inicializando Gráfico de Barras - Financeiro
        const ctxFinanceiro = document.getElementById('chartFinanceiroBarras').getContext('2d');
        new Chart(ctxFinanceiro, {
            type: 'bar',
            data: {
                labels: ['Faturamento Brut.', 'Despesas', 'Lucro Líquido'],
                datasets: [{
                    label: 'Valores em R$',
                    data: [financeiro.faturamento, financeiro.despesas, financeiro.lucro],
                    backgroundColor: ['#3b82f6', '#ef4444', '#10b981'], // Azul (Faturamento), Vermelho (Despesas), Verde (Lucro)
                    borderRadius: 8,
                    borderWidth: 0,
                    barThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return ' R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: {
                            color: '#a1a1aa',
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#a1a1aa' }
                    }
                }
            }
        });
    </script>

</body>
</html>