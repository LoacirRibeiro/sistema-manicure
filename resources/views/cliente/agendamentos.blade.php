<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.5); }
        .card-glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>

{{-- Header --}}
    <header class="p-6 flex justify-between items-center border-b border-zinc-900">
        <a href="#" class="text-2xl font-black tracking-tighter uppercase">Nails<span class="text-neon">Studio</span></a>
        
        @if(request('remarcar_id'))
            <a href="{{ route('admin.painel') }}" class="text-pink-400 hover:text-pink-300 transition text-sm font-semibold uppercase tracking-widest">Voltar ao Painel</a>
        @else
            <a href="{{ route('home.index') }}" class="text-xs font-semibold uppercase tracking-wider border border-pink-500/30 bg-pink-500/10 text-neon px-4 py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-neon hover:text-white hover:shadow-[0_0_15px_rgba(255,0,127,0.5)] flex items-center gap-1.5">
                Home
            </a>
        @endif
    </header>

{{-- Faixa Secundária de Título --}}
<div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-3">
    <div class="flex justify-start mt-4 ml-4 md:ml-10">
        <span class="text-xs uppercase tracking-wider bg-pink-950/50 px-3 py-1 rounded-full text-pink-400 border border-pink-900/50 font-semibold">
            Área do Cliente
        </span>
    </div>
</div>

<body class="text-zinc-200 p-4 md:p-8 space-y-8">

    {{-- Container PAI ÚNICO que define a largura exata de toda a página --}}
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- 1. Banner Informativo (Alinhado) --}}
        <div class="card-glass p-6 rounded-3xl border border-zinc-800/80 bg-zinc-900/10">
            <div class="flex items-center gap-4 text-left">
                <div class="w-12 h-12 rounded-2xl bg-pink-500/10 flex items-center justify-center text-neon text-2xl shrink-0">
                    <i class="la la-clock"></i>
                </div>
                <div>
                    <h4 class="text-base font-bold text-white">Meus Agendamentos</h4>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        Acompanhe aqui o status dos seus agendamentos.<br class="hidden sm:inline">
                        <span class="text-pink-400 font-medium">Nota:</span> Alterações e cancelamentos só são permitidos com até <strong class="text-zinc-200">24 horas de antecedência</strong> e limitado a 1 remarcagem por agendamento.
                    </p>
                </div>
            </div>
        </div>

        {{-- 2. Lista de Agendamentos --}}
        @if($agendamentos->isEmpty())
            <div class="card-glass p-8 rounded-3xl text-center text-zinc-500">
                <i class="la la-calendar-times text-5xl mb-3 text-zinc-700"></i>
                <p>Você não possui nenhum agendamento registrado no momento.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($agendamentos as $agendamento)
                    @php
                        $borderColor = 'border-l-zinc-700';
                        $badgeClass = 'bg-zinc-800 text-zinc-300';
                        $statusText = $agendamento->status;

                        if ($agendamento->status == 'agendado') {
                            $borderColor = 'border-l-pink-500';
                            $badgeClass = 'bg-pink-500/10 text-pink-400 border border-pink-500/20';
                        } elseif ($agendamento->status == 'remarcado') {
                            $borderColor = 'border-l-blue-500';
                            $badgeClass = 'bg-blue-500/10 text-blue-400 border border-blue-500/20';
                            $statusText = 'remarcado';
                        } elseif ($agendamento->status == 'concluido') {
                            $borderColor = 'border-l-emerald-500';
                            $badgeClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                        } elseif ($agendamento->status == 'cancelado') {
                            $borderColor = 'border-l-red-500/40';
                            $badgeClass = 'bg-red-500/10 text-red-400 border border-red-500/20';
                        } elseif ($agendamento->status == 'nao_compareceu') {
                            $borderColor = 'border-l-amber-500';
                            $badgeClass = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
                            $statusText = 'não compareceu';
                        }
                    @endphp

                    <div class="card-glass p-6 rounded-2xl border-l-4 {{ $borderColor }} flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-widest px-2.5 py-1 rounded-lg {{ $badgeClass }}">
                                {{ $statusText }}
                            </span>
                            <h3 class="text-lg font-semibold text-white mt-3">{{ $agendamento->servico->nome ?? 'Procedimento' }}</h3>
                            <p class="text-xs text-zinc-400 mt-1 flex flex-wrap gap-x-4 gap-y-1">
                                <span><i class="la la-user text-pink-500"></i> Profissional: {{ $agendamento->manicure->name ?? 'Especialista' }}</span>
                                <span><i class="la la-calendar text-pink-500"></i> {{ \Carbon\Carbon::parse($agendamento->data_escolhida)->format('d/m/Y') }} às {{ substr($agendamento->hora_escolhida, 0, 5) }}h</span>
                            </p>
                        </div>
                        
                        {{-- AÇÕES DE REMARCAR E CANCELAR --}}
                        @if(in_array($agendamento->status, ['agendado', 'remarcado']))
                            @php
                                $dataHoraAgendamento = \Carbon\Carbon::parse($agendamento->data_escolhida . ' ' . $agendamento->hora_escolhida);
                                $podeAlterar = \Carbon\Carbon::now()->diffInHours($dataHoraAgendamento, false) >= 24;
                            @endphp

                            <div class="w-full md:w-auto flex items-center justify-end gap-2">
                                @if($podeAlterar)
                                    @if($agendamento->qtd_remarcacoes < 1 && $agendamento->status == 'agendado')
                                        <a href="{{ route('cliente.agendamentos.iniciarRemarcacao', $agendamento->id) }}" 
                                        class="text-xs font-semibold uppercase tracking-wider border border-blue-500/30 bg-blue-500/10 text-blue-400 px-3.5 py-2 rounded-xl transition-all duration-300 hover:bg-blue-600 hover:text-white hover:shadow-[0_0_15px_rgba(59,130,246,0.4)] flex items-center gap-1">
                                            <i class="la la-history text-base"></i> Remarcar
                                        </a>
                                    @else
                                        <span class="text-[10px] uppercase font-semibold text-zinc-400 border border-zinc-800 bg-zinc-900/60 px-2.5 py-1.5 rounded-lg" title="Você já utilizou o seu limite de 1 remarcação para este agendamento.">
                                            Já remarcado
                                        </span>
                                    @endif

                                    <form action="{{ route('cliente.agendamentos.cancelar', $agendamento->id) }}" method="POST" class="form-cancelar">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-xs font-semibold uppercase tracking-wider border border-red-500/30 bg-red-500/10 text-red-500 px-3.5 py-2 rounded-xl transition-all duration-300 hover:bg-red-500 hover:text-white hover:shadow-[0_0_15px_rgba(239,68,68,0.4)] flex items-center gap-1">
                                            <i class="la la-trash-alt text-base"></i> Cancelar 
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] uppercase font-semibold text-zinc-500 border border-zinc-800 bg-zinc-900/40 px-3 py-2 rounded-xl flex items-center gap-1 cursor-not-allowed" title="Alterações e cancelamentos só são permitidos com 24h de antecedência.">
                                        <i class="la la-info-circle text-base text-zinc-600"></i> Bloqueado p/ alterações (&lt;24h)
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <script>
        // Intercepta o envio dos formulários de cancelamento
        document.querySelectorAll('.form-cancelar').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Deseja mesmo cancelar?',
                    text: "Essa ação liberará a vaga no sistema e não poderá ser desfeita!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FF007F',
                    cancelButtonColor: '#27272a',
                    confirmButtonText: 'Sim, cancelar!',
                    cancelButtonText: 'Voltar',
                    background: '#121212',
                    color: '#f4f4f5'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Alerta de sucesso
        @if(session('success') || session('sucesso'))
            Swal.fire({
                title: 'Sucesso!',
                text: "{{ session('success') ?? session('sucesso') }}",
                icon: 'success',
                confirmButtonColor: '#FF007F',
                background: '#121212',
                color: '#f4f4f5'
            });
        @endif

        // Alerta de erro
        @if(session('error') || session('erro'))
            Swal.fire({
                title: 'Ops, algo deu errado!',
                text: "{{ session('error') ?? session('erro') }}",
                icon: 'error',
                confirmButtonColor: '#FF007F',
                background: '#121212',
                color: '#f4f4f5'
            });
        @endif
    </script>
</body>
</html>