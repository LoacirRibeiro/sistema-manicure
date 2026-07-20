<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght=300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.3); }
        .bg-neon { background-color: #FF007F; }
        .border-neon { border-color: #FF007F; }
        
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
                <i class="la la-arrow-left text-base"></i> Painel Geral
            </a>
        </div>
    </header>

    {{-- Conteúdo Principal --}}
    <main class="flex-grow max-w-7xl w-full mx-auto p-4 md:p-8">
        
        {{-- Título da Página e Ações --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="la la-users text-neon"></i> Gestão de Clientes
                </h1>
                <p class="text-xs text-zinc-400 mt-1">Acompanhe a atividade das clientes e realize novos agendamentos diretos.</p>
            </div>

            {{-- Botão Clientes Suspensos --}}
            <div>
                <a href="{{ route('admin.clientes.suspensos') }}" class="w-full sm:w-auto text-xs font-semibold uppercase tracking-wider border border-red-950/50 bg-red-950/10 text-red-400 px-5 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-red-900 hover:text-white flex items-center justify-center gap-1.5">
                    <i class="la la-user-slash text-base"></i> Clientes Suspensos
                </a> 
            </div>
        </div>

        {{-- Filtro de Pesquisa de Cliente --}}
        <div class="card-glass p-4 rounded-2xl mb-6">
            <form action="{{ url()->current() }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-grow">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-500">
                        <i class="la la-search text-lg"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, e-mail ou telefone..." class="w-full bg-zinc-900/80 border border-zinc-800 text-white text-sm rounded-xl pl-10 pr-4 py-2.5 focus:border-pink-500 focus:outline-none transition-all placeholder:text-zinc-600">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full sm:w-auto bg-pink-500/10 hover:bg-neon border border-pink-500/30 text-neon hover:text-white px-5 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-300 flex items-center justify-center gap-1">
                        <i class="la la-filter text-base"></i> Filtrar
                    </button>
                    @if(request('search'))
                        <a href="{{ url()->current() }}" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all flex items-center justify-center">
                            Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Mensagem de Sucesso (Flash) --}}
        @if(session('success') || session('sucesso'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-sm flex items-center gap-2">
                <i class="la la-check-circle text-lg"></i> {{ session('success') ?? session('sucesso') }}
            </div>
        @endif

        {{-- Tabela de Usuários em Card Glass --}}
        <div class="card-glass rounded-3xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-900/60 border-b border-zinc-800 text-xs text-zinc-400 uppercase tracking-wider">
                            <th class="p-4 md:p-5">Nome</th>
                            <th class="p-4 md:p-5">E-mail / Telefone</th>
                            <th class="p-4 md:p-5">Status (Últimos 3 meses)</th>
                            <th class="p-4 md:p-5 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50 text-sm">
                        @forelse($usuarios as $user)
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="p-4 md:p-5 font-semibold text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-pink-500/10 border border-pink-500/20 text-neon flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="p-4 md:p-5">
                                    <div class="text-zinc-200">{{ $user->email }}</div>
                                    <small class="text-zinc-500">{{ $user->telefone ?? 'Sem telefone' }}</small>
                                </td>
                                <td class="p-4 md:p-5">
                                    @if(($user->agendamentos_recentes ?? $user->ativos_ultimos_3_meses ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Ativo ({{ $user->agendamentos_recentes ?? $user->ativos_ultimos_3_meses }} agendamento(s))
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-800 text-zinc-400 border border-zinc-700/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-500"></span>
                                            Inativo (+3 meses)
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 md:p-5 text-right">
                                    <a href="{{ route('admin.agendamento.criarParaUsuario', $user->id) }}" 
                                       class="inline-flex items-center gap-1.5 bg-pink-500/10 hover:bg-neon border border-pink-500/30 text-neon hover:text-white px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-300 transform hover:scale-105">
                                        <i class="la la-calendar-plus text-base"></i> Novo Agendamento
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-zinc-500">
                                    <i class="la la-user-slash text-3xl mb-2 block"></i>
                                    Nenhum cliente encontrado com os critérios digitados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Links de Paginação com preservação de query da busca --}}
            @if($usuarios->hasPages())
                <div class="p-4 bg-zinc-900/40 border-t border-zinc-800">
                    {{ $usuarios->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </main>

    {{-- Footer --}}
    <footer class="p-6 bg-zinc-950 border-t border-zinc-900 text-center text-xs text-zinc-600 mt-auto">
        &copy; {{ date('Y') }} NailsStudio. Todos os direitos reservados.
    </footer>

</body>
</html>