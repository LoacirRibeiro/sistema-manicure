<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes Suspensas - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght=300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.3); }
        .bg-neon { background-color: #FF007F; }
        
        /* Customização escura para o SweetAlert2 */
        .swal2-dark-modal {
            background: #121212 !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            border-radius: 24px !important;
            color: #e4e4e7 !important;
        }
        .swal2-dark-input {
            background: #1c1c1e !important;
            border: 1px solid #2d2d30 !important;
            color: #fff !important;
            border-radius: 12px !important;
        }
        .swal2-dark-input:focus {
            border-color: #FF007F !important;
            box-shadow: 0 0 8px rgba(255, 0, 127, 0.4) !important;
        }
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
                <i class="la la-arrow-left"></i> Painel Geral
            </a>
        </div>
    </header>

    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-3">
        <div class="flex justify-start mt-4 ml-4">
            <span class="text-xs uppercase tracking-wider bg-red-950/50 px-3 py-1 rounded-full text-red-400 border border-red-900/50">
                Lista Negra / Suspensões
            </span>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    <main class="flex-grow p-4 md:p-10 max-w-5xl w-full mx-auto space-y-6">

        {{-- Alertas de Feedback --}}
        @if(session('sucesso'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-sm flex items-center gap-2">
                <i class="la la-check-circle text-lg"></i>
                {{ session('sucesso') }}
            </div>
        @endif

        @if(session('erro'))
            <div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-sm flex items-center gap-2">
                <i class="la la-exclamation-circle text-lg"></i>
                {{ session('erro') }}
            </div>
        @endif

        {{-- Explicação de funcionamento --}}
        <div class="card-glass p-5 rounded-2xl border-l-4 border-amber-500 bg-zinc-900/20">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                <i class="la la-info-circle text-amber-500 text-base"></i> Gerenciamento de Penalidades
            </h4>
            <p class="text-xs text-zinc-400 mt-1.5 leading-relaxed">
                As clientes listadas abaixo faltaram a <strong>dois agendamentos consecutivos</strong> e estão sob o "castigo silencioso" de 60 dias. Para elas, o calendário de reservas aparecerá inteiramente lotado e nenhum horário será exibido. Caso ela não entre em contato, o próprio sistema fará o desbloqueio automático ao término do período. Se preferir relevar a falta, utilize o botão "Liberar Cliente".
            </p>
        </div>

        {{-- Tabela de Clientes Suspensas --}}
        <div class="card-glass rounded-3xl border border-zinc-800/80 overflow-hidden">
            <div class="p-6 border-b border-zinc-800 flex justify-between items-center bg-zinc-950/20">
                <h3 class="font-bold text-sm text-white uppercase tracking-wider">Clientes Atualmente de Castigo</h3>
                <span class="text-xs text-zinc-500">Total em punição: <strong class="text-red-400">{{ $clientes->count() }}</strong></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-800 text-[10px] uppercase tracking-wider text-zinc-500 bg-zinc-950/40">
                            <th class="p-4 font-semibold">Cliente</th>
                            <th class="p-4 font-semibold">Contato</th>
                            <th class="p-4 font-semibold">Fim do Castigo</th>
                            <th class="p-4 font-semibold text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900">
                        @if($clientes->isEmpty())
                            <tr>
                                <td colspan="4" class="p-12 text-center text-zinc-500 text-sm">
                                    <i class="la la-user-check text-4xl block mb-2 text-zinc-700"></i>
                                    Nenhuma cliente está de castigo no momento!
                                </td>
                            </tr>
                        @else
                            @foreach($clientes as $cliente)
                                <tr class="hover:bg-zinc-900/20 transition-all">
                                    {{-- Nome da Cliente --}}
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 font-bold text-xs uppercase">
                                                {{ substr($cliente->name, 0, 2) }}
                                            </div>
                                            <span class="text-sm font-bold text-zinc-200">{{ $cliente->name }}</span>
                                        </div>
                                    </td>

                                    {{-- Contato WhatsApp --}}
                                    <td class="p-4">
                                        @if($cliente->whatsapp)
                                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $cliente->whatsapp) }}" target="_blank" class="text-xs text-zinc-400 hover:text-green-400 inline-flex items-center gap-1.5 transition">
                                                <i class="la la-whatsapp text-green-500 text-base"></i> Enviar Mensagem
                                            </a>
                                        @else
                                            <span class="text-xs text-zinc-600">Sem telefone</span>
                                        @endif
                                    </td>

                                    {{-- Data de Liberação Automática --}}
                                    <td class="p-4">
                                        <span class="block text-xs font-bold text-white">
                                            {{ $cliente->bloqueado_ate->format('d/m/Y') }}
                                        </span>
                                        <span class="text-[10px] text-zinc-500">
                                            Liberação automática em {{ $cliente->bloqueado_ate->diffForHumans() }}
                                        </span>
                                    </td>

                                    {{-- Botão de Desbloqueio Antecipado --}}
                                    <td class="p-4 text-right">
                                        <form id="form-desbloqueio-{{ $cliente->id }}" action="{{ route('admin.clientes.desbloquear', $cliente->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="admin_password" id="input-senha-{{ $cliente->id }}">
                                            <button type="button" onclick="confirmarDesbloqueio('{{ $cliente->id }}', '{{$cliente->name }}')" class="bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400 hover:text-white border border-emerald-500/30 px-3.5 py-1.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-300">
                                                <i class="la la-unlock text-sm"></i> Liberar Cliente
                                            </button>
                                        </form>
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

    {{-- Script de Manipulação do SweetAlert2 --}}
    <script>
        function confirmarDesbloqueio(clienteId, clienteNome) {
            Swal.fire({
                title: 'Desbloquear Cliente?',
                html: `Você está liberando os agendamentos online para <strong class="text-neon">${clienteNome}</strong>.<br><br>Por favor, digite sua <strong>Senha de Administrador</strong> para confirmar:`,
                icon: 'warning',
                input: 'password',
                inputPlaceholder: 'Sua senha administrativa',
                showCancelButton: true,
                confirmButtonText: 'Confirmar Liberação',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal2-dark-modal',
                    input: 'swal2-dark-input',
                    confirmButton: 'bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-5 rounded-xl text-xs uppercase tracking-wider transition mr-2',
                    cancelButton: 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-2.5 px-5 rounded-xl text-xs uppercase tracking-wider transition'
                },
                buttonsStyling: false,
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'Você precisa digitar sua senha!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Passa a senha preenchida no modal para o input hidden correspondente
                    document.getElementById('input-senha-' + clienteId).value = result.value;
                    // Envia o formulário
                    document.getElementById('form-desbloqueio-' + clienteId).submit();
                }
            });
        }
    </script>

</body>
</html>