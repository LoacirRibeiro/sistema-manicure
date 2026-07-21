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
<body class="text-zinc-200 p-6 md:p-12">

    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Meus <span class="text-neon">Agendamentos</span></h1>
            <a href="{{ route('home.index') }}" class="text-xs font-semibold uppercase tracking-wider border border-pink-500/30 bg-pink-500/10 text-neon px-4 py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-neon hover:text-white hover:shadow-[0_0_15px_rgba(255,0,127,0.5)] flex items-center gap-1.5">
                Home
            </a>
        </div>

        @if($agendamentos->isEmpty())
            <div class="card-glass p-8 rounded-3xl text-center text-zinc-500">
                <i class="la la-calendar-times text-5xl mb-3 text-zinc-700"></i>
                <p>Você não possui nenhum agendamento registrado.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($agendamentos as $agendamento)
                    @php
                        // Configuração padrão de cores (Fallback caso venha outro status)
                        $borderColor = 'border-l-zinc-700';
                        $badgeClass = 'bg-zinc-800 text-zinc-300';
                        $statusText = $agendamento->status;

                        // Cores específicas para cada status
                        if ($agendamento->status == 'agendado') {
                            $borderColor = 'border-l-pink-500';
                            $badgeClass = 'bg-pink-500/10 text-pink-400 border border-pink-500/20';
                        } elseif ($agendamento->status == 'concluido') {
                            $borderColor = 'border-l-emerald-500';
                            $badgeClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                        } elseif ($agendamento->status == 'cancelado') {
                            $borderColor = 'border-l-red-500/40';
                            $badgeClass = 'bg-red-500/10 text-red-400 border border-red-500/20';
                        } elseif ($agendamento->status == 'nao_compareceu') {
                            // Estilização para o caso de Falta / Não Compareceu
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
                            <p class="text-xs text-zinc-400 mt-1">
                                <span class="mr-4"><i class="la la-user text-pink-500"></i> Profissional: {{ $agendamento->manicure->name ?? 'Especialista' }}</span>
                                <span><i class="la la-calendar text-pink-500"></i> {{ \Carbon\Carbon::parse($agendamento->data_escolhida)->format('d/m/Y') }} às {{ substr($agendamento->hora_escolhida, 0, 5) }}h</span>
                            </p>
                        </div>
                        
                        {{-- AÇÃO DE CANCELAR --}}
                        @if($agendamento->status == 'agendado')
                            @php
                                $dataHoraAgendamento = \Carbon\Carbon::parse($agendamento->data_escolhida . ' ' . $agendamento->hora_escolhida);
                                $podeCancelar = \Carbon\Carbon::now()->diffInHours($dataHoraAgendamento, false) >= 24;
                            @endphp

                            <div class="w-full md:w-auto flex justify-end">
                                @if($podeCancelar)
                                    <form action="{{ route('cliente.agendamentos.cancelar', $agendamento->id) }}" method="POST" class="form-cancelar">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-xs font-semibold uppercase tracking-wider border border-red-500/30 bg-red-500/10 text-red-500 px-4 py-2 rounded-xl transition-all duration-300 hover:bg-red-500 hover:text-white hover:shadow-[0_0_15px_rgba(239,68,68,0.4)] flex items-center gap-1">
                                            <i class="la la-trash-alt text-base"></i> Cancelar Agendamento
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] uppercase font-semibold text-zinc-500 border border-zinc-800 bg-zinc-900/40 px-3 py-2 rounded-xl flex items-center gap-1 cursor-not-allowed" title="Cancelamentos só são permitidos com 24h de antecedência.">
                                        <i class="la la-info-circle text-base text-zinc-600"></i> Bloqueado p/ cancelamento
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
                e.preventDefault(); // Trava o envio para mostrar o SweetAlert

                Swal.fire({
                    title: 'Deseja mesmo cancelar?',
                    text: "Essa ação não poderá ser desfeita no sistema!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FF007F', // Rosa Neon
                    cancelButtonColor: '#27272a',  // zinc-800
                    confirmButtonText: 'Sim, cancelar!',
                    cancelButtonText: 'Voltar',
                    background: '#121212',         // Fundo escuro
                    color: '#f4f4f5'               // Texto claro
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Envia o formulário se confirmado
                    }
                });
            });
        });

        // Mostra o alerta de sucesso se houver na sessão do Laravel
        @if(session('success'))
            Swal.fire({
                title: 'Sucesso!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#FF007F',
                background: '#121212',
                color: '#f4f4f5'
            });
        @endif

        // Mostra o alerta de erro se houver na sessão do Laravel
        @if(session('error'))
            Swal.fire({
                title: 'Ops, algo deu errado!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#FF007F',
                background: '#121212',
                color: '#f4f4f5'
            });
        @endif
    </script>
</body>
</html>