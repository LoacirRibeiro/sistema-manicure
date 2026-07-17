<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nails Design - Especialista em Alongamentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .font-title { font-family: 'Playfair Display', serif; }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.5); }
        .bg-neon { background-color: #FF007F; box-shadow: 0 0 20px rgba(255, 0, 127, 0.4); }
        .border-neon { border-color: #FF007F; }
        .card-glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="text-zinc-200">

    {{-- HEADER --}}
    <header class="p-6 border-b border-zinc-800 relative bg-[#0f0f0f]">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-black tracking-tighter uppercase">Nails<span class="text-neon">Studio</span></h1>
            
            <nav class="hidden md:flex items-center gap-8 font-semibold text-sm uppercase tracking-widest">
                <a href="#sobre-nos" class="hover:text-neon transition">Sobre Nós</a>
                <a href="#servicos" class="hover:text-neon transition">Serviços</a>
                <a href="#contato" class="hover:text-neon transition">Contato</a>
                
                @auth
                    @role('admin')
                        <a href="{{ route('admin.painel') }}" class="flex items-center gap-1.5 border border-pink-500/20 bg-pink-500/5 hover:bg-pink-500/10 text-neon px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                            <i class="la la-cog text-sm"></i>
                            Painel Admin
                        </a>
                    @endrole

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-zinc-500 hover:text-red-400 text-xs font-bold uppercase tracking-wider transition-all">
                            Sair
                        </button>
                    </form>

                    <span class="text-zinc-400 normal-case tracking-normal font-normal text-xs">
                        Olá, <span class="text-white font-semibold">{{ auth()->user()->name }}</span>!
                    </span>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="flex items-center gap-2 border border-zinc-800 bg-zinc-900/50 hover:border-pink-500/50 hover:text-white text-zinc-300 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                        <i class="la la-user text-sm text-neon"></i>
                        Entrar
                    </a>
                @endguest
            </nav>

            <button id="btn-menu" class="md:hidden text-2xl text-zinc-200 hover:text-neon focus:outline-none transition">
                <i class="la la-bars"></i>
            </button>
        </div>

        <div id="menu-mobile" class="hidden md:hidden mt-4 pt-4 border-t border-zinc-800 flex-col gap-4 font-semibold text-sm uppercase tracking-widest">
            <a href="#sobre-nos" class="hover:text-neon py-2 transition block">Sobre Nós</a>
            <a href="#servicos" class="hover:text-neon py-2 transition block">Serviços</a>
            <a href="#contato" class="hover:text-neon py-2 transition block">Contato</a>
            
            @auth
                @role('admin')
                    <a href="{{ route('admin.painel') }}" class="flex items-center justify-center gap-1.5 border border-pink-500/20 bg-pink-500/5 hover:bg-pink-500/10 text-neon py-2.5 rounded-lg text-xs font-bold transition-all w-full">
                        <i class="la la-cog text-sm"></i>
                        Painel Admin
                    </a>
                @endrole

                <div class="flex justify-between items-center py-2 border-t border-zinc-800/50 mt-2">
                    <span class="text-zinc-400 normal-case tracking-normal font-normal text-xs">
                        Olá, <span class="text-white font-semibold">{{ auth()->user()->name }}</span>!
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-zinc-500 hover:text-red-400 text-xs font-bold uppercase tracking-wider transition-all">
                            Sair
                        </button>
                    </form>
                </div>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 border border-zinc-800 bg-zinc-900/50 hover:border-pink-500/50 hover:text-white text-zinc-300 py-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-all w-full">
                    <i class="la la-user text-sm text-neon"></i>
                    Entrar
                </a>
            @endguest
        </div>
    </header>

    <script>
        const btnMenu = document.getElementById('btn-menu');
        const menuMobile = document.getElementById('menu-mobile');

        btnMenu.addEventListener('click', () => {
            if (menuMobile.classList.contains('hidden')) {
                menuMobile.classList.remove('hidden');
                menuMobile.classList.add('flex');
            } else {
                menuMobile.classList.remove('flex');
                menuMobile.classList.add('hidden');
            }
        });
    </script>

    @if(auth()->check() && isset($agendamentoAtivo))
        <div class="max-w-6xl mx-auto px-6 mt-8">
            <div class="card-glass p-6 rounded-3xl border border-pink-500/30 bg-gradient-to-r from-pink-500/10 via-transparent to-transparent flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden shadow-[0_0_25px_rgba(255,0,127,0.1)]">
                
                {{-- Detalhes do Agendamento --}}
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-pink-500/20 flex items-center justify-center text-neon text-2xl shrink-0 animate-bounce">
                        <i class="la la-calendar-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-black tracking-widest text-neon">Seu Próximo Agendamento</span>
                        <h4 class="text-lg font-bold text-white mt-0.5">
                            {{ $agendamentoAtivo->servico->nome ?? 'Procedimento Estético' }}
                        </h4>
                        <p class="text-xs text-zinc-400 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                            <span class="flex items-center gap-1">
                                <i class="la la-user text-pink-400 text-sm"></i> 
                                Profissional: <strong class="text-zinc-200 font-medium">{{ $agendamentoAtivo->manicure->name ?? 'Especialista' }}</strong>
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="la la-clock text-pink-400 text-sm"></i> 
                                {{ \Carbon\Carbon::parse($agendamentoAtivo->data_escolhida)->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($agendamentoAtivo->hora_escolhida)->format('H:i') }}h
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Ações rápidas do Card --}}
                <div class="flex items-center gap-3 w-full md:w-auto justify-end border-t border-zinc-800/50 md:border-none pt-4 md:pt-0">
                    <span class="text-[10px] uppercase font-bold tracking-wider bg-zinc-800/80 text-zinc-300 px-3 py-1.5 rounded-lg border border-zinc-700/50">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-ping"></span>
                        Confirmado
                    </span>
                    
                    <a href="{{ route('cliente.agendamentos') }}" class="bg-pink-500/10 text-neon font-bold text-xs px-3 py-1 rounded-full uppercase tracking-widest border border-pink-500/20">
                        Ver Detalhes 
                    </a>
                </div>

                <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-pink-600 rounded-full filter blur-[50px] opacity-20 pointer-events-none"></div>
            </div>
        </div>
    @endif

    {{-- BANNER PRINCIPAL (HERO) --}}
    <section class="relative py-20 px-6 overflow-hidden">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-neon font-bold tracking-widest uppercase text-sm">Expert em Alongamentos</span>
                <h2 class="text-6xl md:text-8xl font-title mt-4 leading-tight">Suas unhas, <br> sua <span class="italic">assinatura.</span></h2>
                <p class="mt-6 text-zinc-400 text-lg max-w-md">Especialista em técnicas avançadas de fibra de vidro e gel, unindo resistência e naturalidade para mulheres empoderadas.</p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('portfolio') }}" class="flex items-center justify-center gap-2 border border-pink-500/30 bg-pink-500/10 text-neon px-8 py-4 rounded-xl hover:bg-pink-500/20 transition">
                        Galeria
                    </a>

                    <a href="{{ route('cliente.agendamentos') }}" class="flex items-center justify-center gap-2 border border-pink-500/30 bg-pink-500/10 text-neon px-8 py-4 rounded-xl hover:bg-pink-500/20 transition">
                        Meus Horários
                    </a>

                    <a href="{{ route('agendamento.horarios') }}" class="flex items-center justify-center gap-2 border border-pink-500/30 bg-pink-500/10 text-neon px-8 py-4 rounded-xl hover:bg-pink-500/20 transition">
                        Agendar Horário
                    </a>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -top-10 -left-10 w-64 h-64 bg-purple-600 rounded-full filter blur-[120px] opacity-20"></div>
                
                {{-- Foto Hero Local Manual --}}
                <div class="rounded-3xl border-2 border-neon p-2 transform rotate-3">
                   <img src="{{ asset('img/marcielle.jpg') }}" class="rounded-2xl shadow-2xl" alt="Unhas Maravilhosas">
                </div>

                {{-- COMENTADO PARA SUBIR IMAGENS MANUALMENTE 
                <div class="rounded-3xl border-2 border-neon p-2 transform rotate-3">
                   <img src="{{ isset($configuracoes->foto_hero) ? asset('storage/' . $configuracoes->foto_hero) : 'https://images.unsplash.com/photo-1604654894610-df63bc536371?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80' }}" class="rounded-2xl shadow-2xl" alt="Unhas Maravilhosas">
                </div> 
                --}}
            </div>
        </div>
    </section>

    {{-- LISTAGEM DE SERVIÇOS --}}
    <section id="servicos" class="py-20 px-6 bg-zinc-900/30">
        <div class="max-w-6xl mx-auto text-center mb-16">
            <h3 class="text-4xl font-title">Nossos <span class="text-neon">Serviços</span></h3>
            <p class="text-zinc-500 mt-2">Escolha a técnica ideal para o seu estilo de vida</p>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($servicos as $servico)
            <div class="card-glass rounded-3xl hover:border-neon transition group flex flex-col overflow-hidden">
                
                {{-- Foto do Procedimento (Buscando de public/img/) --}}
                <div class="w-full h-48 bg-zinc-900 overflow-hidden relative">
                    @if($servico->foto_exemplo)
                        <img src="{{ asset('img/' . $servico->foto_exemplo) }}" alt="{{ $servico->nome }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.onerror=null;this.src='{{ asset('storage/' . $servico->foto_exemplo) }}';">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-700 bg-zinc-950">
                            <i class="la la-image text-5xl"></i>
                        </div>
                    @endif

                    <div class="absolute bottom-3 left-3 w-10 h-10 bg-zinc-900/80 backdrop-blur-sm rounded-xl flex items-center justify-center text-neon text-xl group-hover:bg-neon group-hover:text-white transition">
                        <i class="la la-magic"></i>
                    </div>
                </div>

                {{-- Conteúdo do Texto --}}
                <div class="p-6 flex flex-col flex-grow">
                    <h4 class="text-xl font-bold mb-2">{{ $servico->nome }}</h4>
                    <p class="text-zinc-500 text-xs leading-relaxed mb-6 line-clamp-2" title="{{ $servico->descricao }}">
                        {{ $servico->descricao }}
                    </p>
                    
                    <div class="flex justify-between items-center mt-auto pt-2 border-t border-zinc-900">
                        <span class="text-neon font-black text-lg">R$ {{ number_format($servico->preco, 2, ',', '.') }}</span>
                        <a href="{{ route('agendamento.horarios', ['servico_id' => $servico->id]) }}" class="text-[10px] uppercase font-bold tracking-widest border border-pink-500/30 bg-pink-500/10 text-neon px-4 py-2 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-neon hover:text-white hover:shadow-[0_0_15px_rgba(255,0,127,0.5)]">
                            Agendar
                        </a>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </section>

    {{-- SEÇÃO: SOBRE NÓS --}}
    <section id="sobre-nos" class="py-24 px-6 relative max-w-6xl mx-auto">
        <div class="absolute -left-10 top-20 w-48 h-48 bg-pink-600 rounded-full filter blur-[120px] opacity-10 pointer-events-none"></div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="space-y-4">
                <span class="text-neon font-bold tracking-widest uppercase text-xs">Quem Somos</span>
                <h2 class="text-3xl md:text-4xl font-title text-white">Arte, Cuidado e Autoestima em Cada Detalhe</h2>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    No <span class="text-zinc-200 font-semibold">NailsStudio</span>, não fazemos apenas unhas; nós criamos experiências de beleza personalizadas. Nossa equipe é composta por profissionais especialistas em alongamentos, nail art feitas à mão livre e tratamentos premium para a saúde das suas mãos.
                </p>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    Utilizamos materiais de altíssima qualidade, rigorosamente esterilizados, garantindo um resultado impecável, duradouro e totalmente seguro para você brilhar.
                </p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                    <div class="card-glass p-4 rounded-2xl border-l-2 border-neon">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Material Premium</h4>
                        <p class="text-[11px] text-zinc-400 mt-1">Esmaltes em gel e insumos das melhores marcas do mercado.</p>
                    </div>
                    <div class="card-glass p-4 rounded-2xl border-l-2 border-neon">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Biossegurança</h4>
                        <p class="text-[11px] text-zinc-400 mt-1">100% dos materiais metálicos são rigorosamente autoclavados.</p>
                    </div>
                </div>
            </div>

            <div class="relative rounded-3xl overflow-hidden card-glass p-2 aspect-[4/3]">
                {{-- Exibição Manual da Imagem Local do Salão --}}
                <img src="{{ asset('img/foto_do_salao.jpg') }}" class="w-full h-full object-cover rounded-2xl transition duration-500" alt="Espaço NailsStudio">

                {{-- COMENTADO PARA SUBIR IMAGENS MANUALMENTE 
                @if(isset($configuracoes->foto_espaco))
                    @else
                    <div class="w-full h-full rounded-2xl bg-zinc-900 border border-zinc-800/50 flex flex-col items-center justify-center text-center p-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-tr from-pink-500/10 to-transparent opacity-50"></div>
                        <i class="la la-gem text-5xl text-neon mb-4"></i>
                        <h3 class="font-title text-xl text-white">Espaço NailsStudio</h3>
                        <p class="text-xs text-zinc-500 max-w-xs mt-2">Um ambiente planejado para o seu conforto, com atendimento exclusivo e café gourmet aguardando por você.</p>
                    </div>
                @endif
                --}}
            </div>
        </div>
    </section>

    {{-- SEÇÃO: CONTATO E LOCALIZAÇÃO --}}
    <section id="contato" class="py-24 px-6 border-t border-zinc-900/80 bg-zinc-900/10">
        <div class="max-w-6xl mx-auto">
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-neon font-bold tracking-widest uppercase text-xs">Fale Conosco</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                {{-- WhatsApp --}}
                <a href="https://wa.me/5563992185324" target="_blank" class="card-glass p-6 rounded-2xl flex items-start gap-4 transition duration-300 hover:border-pink-500/40 group">
                    <div class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center text-neon group-hover:bg-neon group-hover:text-white transition duration-300 text-2xl">
                        <i class="la la-whatsapp"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider">WhatsApp</h4>
                        <p class="text-xs text-zinc-400 mt-1">(63) 99218-5324</p>
                        <span class="text-[10px] text-pink-400 font-semibold mt-2 block group-hover:underline">Clique para conversar →</span>
                    </div>
                </a>

                {{-- NOVO: Instagram --}}
                <a href="https://www.instagram.com/marcielle_nail?utm_source=qr&igsh=enF1YzhydDN6OGRk" target="_blank" class="card-glass p-6 rounded-2xl flex items-start gap-4 transition duration-300 hover:border-pink-500/40 group">
                    <div class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center text-neon group-hover:bg-neon group-hover:text-white transition duration-300 text-2xl">
                        <i class="la la-instagram"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider">Instagram</h4>
                        <p class="text-xs text-zinc-400 mt-1">@marcielle_nail</p>
                        <span class="text-[10px] text-pink-400 font-semibold mt-2 block group-hover:underline">Siga nosso perfil →</span>
                    </div>
                </a>

                {{-- Horários --}}
                <div class="card-glass p-6 rounded-2xl flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center text-neon text-2xl">
                        <i class="la la-clock"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider">Horários</h4>
                        <p class="text-xs text-zinc-400 mt-1">Segunda a Terça : 09h às 17h</p>
                        <p class="text-xs text-zinc-400 mt-1">Sábado : 08h às 16h</p>
                        <p class="text-[10px] text-zinc-500 mt-1">Atendimento exclusivo com hora marcada.</p>
                    </div>
                </div>

                {{-- Endereço --}}
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode('Alameda 19 Arso 131, 57, Palmas, 77019-704, TO') }}" target="_blank" class="card-glass p-6 rounded-2xl flex items-start gap-4 transition duration-300 hover:border-pink-500/40 group">
                    <div class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center text-neon group-hover:bg-neon group-hover:text-white transition duration-300 text-2xl">
                        <i class="la la-map-marker"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider">Nosso Espaço</h4>
                        <p class="text-xs text-zinc-400 mt-1 line-clamp-2">Alameda 19 Arso 131, 57, Palmas, 77019-704, TO, BR</p>
                        <span class="text-[10px] text-pink-400 font-semibold mt-2 block group-hover:underline">Ver no mapa →</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- RODAPÉ --}}
    <footer class="py-10 border-t border-zinc-900 text-center text-zinc-600 text-sm">
        <p>&copy; {{ date('Y') }} NailsStudio Design. Todos os direitos reservados.</p>
    </footer>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('sucesso'))
            Swal.fire({
                title: 'Agendado com Sucesso!',
                text: "{{ session('sucesso') }}",
                icon: 'success',
                background: '#121214',
                color: '#e4e4e7',
                confirmButtonColor: '#FF007F',
                confirmButtonText: 'Maravilha!',
                customClass: {
                    popup: 'border border-zinc-800 rounded-3xl'
                }
            });
        @endif
    </script>

</body>
</html>