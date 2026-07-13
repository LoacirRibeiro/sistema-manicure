<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria de Inspirações - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0b0b0b; }
        .font-title { font-family: 'Playfair Display', serif; }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.3); }
        .bg-neon { background-color: #FF007F; box-shadow: 0 0 20px rgba(255, 0, 127, 0.4); }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex flex-col justify-between">

    {{-- Header --}}
    <header class="p-6 flex justify-between items-center border-b border-zinc-900 bg-zinc-950/40 backdrop-blur-md sticky top-0 z-40">
        <a href="{{ route('home.index') }}" class="text-2xl font-black tracking-tighter uppercase">Nails<span class="text-neon">Studio</span></a>
        <a href="{{ route('agendamento.horarios') }}" class="bg-neon text-white text-xs font-bold px-5 py-3 rounded-xl uppercase tracking-widest transition transform hover:scale-105 active:scale-95">Agendar Horário</a>
    </header>

    {{-- Conteúdo Principal --}}
    <main class="py-12 px-4 max-w-6xl mx-auto w-full flex-grow">
        
        <div class="text-center max-w-2xl mx-auto mb-12 relative">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-40 bg-pink-600 rounded-full filter blur-[100px] opacity-10 pointer-events-none"></div>
            <span class="text-neon font-bold tracking-widest uppercase text-xs">Feed de Tendências</span>
            <h1 class="text-4xl md:text-5xl font-title mt-2">Nossas Inspirações</h1>
            <p class="text-sm text-zinc-400 mt-4">
                Explore as criações exclusivas do nosso estúdio. Clique em qualquer publicação para ver os detalhes da aplicação e a profissional responsável.
            </p>
        </div>

        {{-- Grid de Fotos Estilo Galeria/Instagram --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($fotos as $item)
                <div onclick="abrirModal('{{ asset('storage/' . $item->caminho_foto) }}', '{{ $item->titulo }}', '{{ $item->legenda }}', '{{ $item->manicure->name ?? '' }}')" 
                     class="card-glass rounded-xl overflow-hidden group relative aspect-square transition duration-300 hover:border-pink-500/40 cursor-zoom-in shadow-lg">
                    
                    <img src="{{ asset('storage/' . $item->caminho_foto) }}" 
                         alt="{{ $item->titulo ?? 'Trabalho NailsStudio' }}" 
                         class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    
                    {{-- Hover clean simulando interações --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-2">
                        <div class="bg-black/60 backdrop-blur-md px-4 py-2 rounded-full text-xs font-medium tracking-wide border border-white/10 flex items-center gap-1.5">
                            <i class="la la-eye text-pink-500 text-sm"></i> Visualizar detalhes
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 card-glass rounded-3xl">
                    <i class="la la-camera text-5xl text-zinc-700 mb-3 block"></i>
                    <p class="text-sm text-zinc-500">Nenhuma foto adicionada à nossa galeria ainda.</p>
                </div>
            @endforelse
        </div>

    </main>

    {{-- 🪄 NOVO MODAL ESTILO INSTAGRAM (IMAGEM GRANDE + SIDEBAR LATERAL) --}}
    <div id="fotoModal" onclick="fecharModal()" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 cursor-zoom-out">
        
        {{-- Botão Fechar no Topo Direito da Tela --}}
        <button class="absolute top-4 right-4 text-zinc-400 hover:text-white text-3xl transition z-50" onclick="fecharModal()">
            <i class="la la-times"></i>
        </button>

        {{-- Container do Feed/Post --}}
        <div onclick="event.stopPropagation()" class="bg-zinc-950 w-full max-w-4xl h-fit max-h-[90vh] md:h-[600px] rounded-2xl overflow-hidden shadow-2xl border border-zinc-800 flex flex-col md:flex-row cursor-default">
            
            {{-- Lado Esquerdo: Área Dedicada à Foto (Ocupa o espaço máximo disponível de forma inteligente) --}}
            <div class="w-full md:w-[60%] h-[350px] md:h-full bg-zinc-900 flex items-center justify-center overflow-hidden border-b md:border-b-0 md:border-r border-zinc-900">
                <img id="modalImagem" src="" alt="Ampliada" class="w-full h-full object-contain">
            </div>
            
            {{-- Lado Direito: Detalhes do Post (Fixo e Scannable igual ao Instagram) --}}
            <div class="w-full md:w-[40%] flex flex-col h-[calc(100%-350px)] md:h-full justify-between bg-zinc-950 p-6">
                
                <div class="space-y-4 overflow-y-auto pr-1 flex-grow">
                    {{-- Header Interno: Identidade da Profissional --}}
                    <div class="flex items-center gap-3 pb-3 border-b border-zinc-900">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-pink-500 to-yellow-500 p-0.5 flex items-center justify-center shadow-md">
                            <div class="w-full h-full bg-zinc-950 rounded-full flex items-center justify-center">
                                <i class="la la-user-astronaut text-pink-500 text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-widest text-zinc-500 block">Profissional</span>
                            <h4 id="modalManicure" class="text-sm font-semibold text-white tracking-wide"></h4>
                        </div>
                    </div>

                    {{-- Conteúdo: Título e Legenda --}}
                    <div class="space-y-2 pt-1">
                        <h3 id="modalTitulo" class="text-base font-bold text-zinc-100 tracking-tight leading-snug"></h3>
                        <p id="modalLegenda" class="text-xs text-zinc-400 leading-relaxed"></p>
                    </div>
                </div>

                {{-- CTA Inferior: Incentivo de Conversão Direct --}}
                <div class="pt-4 border-t border-zinc-900 mt-auto flex flex-col gap-2">
                    <div class="text-[11px] text-zinc-500 flex items-center gap-1">
                        <i class="la la-heart text-pink-500"></i> Amou esse resultado? Garanta a sua vaga!
                    </div>
                    <a href="{{ route('agendamento.horarios') }}" class="w-full bg-zinc-900 border border-zinc-800 text-white text-center text-xs font-bold py-3 rounded-xl uppercase tracking-wider hover:bg-zinc-800 transition">
                        Agendar com esta Profissional
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="py-6 border-t border-zinc-900 text-center text-zinc-600 text-xs">
        <p>&copy; {{ date('Y') }} NailsStudio Design. Todos os direitos reservados.</p>
    </footer>

    {{-- SCRIPT DE GERENCIAMENTO --}}
    <script>
        function abrirModal(src, titulo, legenda, manicure) {
            const modal = document.getElementById('fotoModal');
            
            document.getElementById('modalImagem').src = src;
            document.getElementById('modalTitulo').innerText = titulo || 'Inspiração NailsStudio';
            document.getElementById('modalLegenda').innerText = legenda || 'Sem descrição adicional disponível.';
            document.getElementById('modalManicure').innerText = manicure || 'Equipe NailsStudio';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function fecharModal() {
            const modal = document.getElementById('fotoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModal();
            }
        });
    </script>
</body>
</html>