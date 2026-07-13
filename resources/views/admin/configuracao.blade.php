<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações da Landing Page - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .font-title { font-family: 'Playfair Display', serif; }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.5); }
        .bg-neon { background-color: #FF007F; box-shadow: 0 0 20px rgba(255, 0, 127, 0.4); }
        .card-glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-2xl w-full card-glass p-8 rounded-3xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-pink-600 rounded-full filter blur-[80px] opacity-20"></div>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-zinc-900">
            <div>
                <h2 class="text-3xl font-bold tracking-tighter uppercase">Configurar <span class="text-neon">Imagens</span></h2>
                <p class="text-zinc-400 text-xs mt-1">Altere o visual da página inicial do seu estúdio</p>
            </div>
            
            <a href="{{ route('admin.painel') }}" class="text-xs font-semibold uppercase tracking-wider border border-pink-500/30 bg-pink-500/10 text-neon px-4 py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-neon hover:text-white hover:shadow-[0_0_15px_rgba(255,0,127,0.5)] flex items-center gap-1.5">
                Voltar ao Painel
            </a>
        </div>

        {{-- Alertas Nativos do Backend --}}
        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.landing.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Campo: Foto Inicial (Hero) --}}
                <div class="space-y-2">
                    <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400">Foto Inicial (Hero)</label>
                    <div class="card-glass p-4 rounded-2xl border-dashed border-2 border-zinc-800 hover:border-neon transition text-center relative group cursor-pointer">
                        <input type="file" name="foto_hero" id="foto_hero" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer text-[0]">
                        <div class="space-y-1 py-4">
                            <i class="la la-cloud-upload-alt text-3xl text-zinc-500 group-hover:text-neon transition"></i>
                            <p class="text-xs font-medium text-zinc-300">Selecione a imagem</p>
                            <p class="text-[10px] text-zinc-500">Proporção sugerida: Vertical (3:4)</p>
                        </div>
                    </div>
                </div>

                {{-- Campo: Foto do Espaço --}}
                <div class="space-y-2">
                    <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400">Foto do Espaço</label>
                    <div class="card-glass p-4 rounded-2xl border-dashed border-2 border-zinc-800 hover:border-neon transition text-center relative group cursor-pointer">
                        <input type="file" name="foto_espaco" id="foto_espaco" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer text-[0]">
                        <div class="space-y-1 py-4">
                            <i class="la la-images text-3xl text-zinc-500 group-hover:text-neon transition"></i>
                            <p class="text-xs font-medium text-zinc-300">Selecione a imagem</p>
                            <p class="text-[10px] text-zinc-500">Proporção sugerida: Horizontal (4:3)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-900 flex justify-end">
                <button type="submit" class="text-xs font-semibold uppercase tracking-wider border border-pink-500/30 bg-pink-500/10 text-neon px-4 py-2.5 rounded-xl transition-all duration-300 transform hover:scale-105 hover:bg-neon hover:text-white hover:shadow-[0_0_15px_rgba(255,0,127,0.5)] flex items-center gap-1.5">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    {{-- Script de Alerta de Sucesso --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('sucesso'))
            Swal.fire({
                title: 'Atualizado!',
                text: "{{ session('sucesso') }}",
                icon: 'success',
                background: '#121214',
                color: '#e4e4e7',
                confirmButtonColor: '#FF007F',
                confirmButtonText: 'Entendido',
                customClass: { popup: 'border border-zinc-800 rounded-3xl' }
            });
        @endif
    </script>
</body>
</html>