<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.5); }
        .bg-neon { background-color: #FF007F; box-shadow: 0 0 20px rgba(255, 0, 127, 0.4); }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full card-glass p-8 rounded-3xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-pink-600 rounded-full filter blur-[80px] opacity-15"></div>
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-black uppercase tracking-tighter">Nails<span class="text-neon">Studio</span></h2>
            <p class="text-zinc-400 text-sm mt-2">Acesse sua conta para gerenciar seus agendamentos</p>
        </div>

        {{-- Bloco para Exibição de Mensagens Backend (Laravel) --}}
        @if ($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs space-y-1">
                <strong>Erro ao entrar:</strong>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('sucesso'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold">
                {{ session('sucesso') }}
            </div>
        @endif

        {{-- Container para Erros Dinâmicos de Validação do Frontend (JS) --}}
        <div id="erro-container" class="hidden mb-4 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-medium"></div>

        <form id="formLogin" action="{{ route('login') }}" method="POST" class="space-y-5" novalidate>
            @csrf
            
            <div>
                <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-2">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="seu@email.com" 
                       class="w-full bg-zinc-900/60 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-neon transition text-sm">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-2">Senha</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" 
                       class="w-full bg-zinc-900/60 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-neon transition text-sm">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-zinc-400 hover:text-zinc-200">
                    <input type="checkbox" name="remember" class="accent-pink-500 rounded text-pink-500"> Lembrar de mim
                </label>
                <a href="{{ route('password.request') }}" class="text-zinc-400 hover:text-neon transition">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="w-full bg-neon text-white font-bold py-3.5 rounded-xl transition transform hover:scale-[1.01] active:scale-[0.99] uppercase tracking-widest text-xs mt-2">
                Entrar no Sistema
            </button>
        </form>

        <div class="text-center mt-6 text-xs text-zinc-500">
            Não tem uma conta? <a href="{{ route('cadastro') }}" class="text-neon font-semibold hover:underline">Cadastre-se</a>
        </div>
    </div>

    <script>
        // --- VALIDAÇÃO ANTES DO ENVIO (Inline Alert) ---
        document.getElementById('formLogin').addEventListener('submit', function(e) {
            const erroContainer = document.getElementById('erro-container');
            
            // Limpa mensagens anteriores
            erroContainer.classList.add('hidden');
            erroContainer.innerText = '';

            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('password').value;
            let mensajeErro = '';

            // Validação de campos vazios
            if (!email || !senha) {
                mensajeErro = 'Por favor, preencha todos os campos para fazer o login.';
            } 
            // Validação da estrutura do e-mail
            else if (!linearEmailValid(email)) {
                mensajeErro = 'Por favor, insira um formato de e-mail válido.';
            }

            // Se houver algum erro, interrompe o envio e mostra a mensagem
            if (mensajeErro !== '') {
                e.preventDefault();
                erroContainer.innerText = mensajeErro;
                erroContainer.classList.remove('hidden');
            }
        });

        // Função de verificação Regex para e-mail
        function linearEmailValid(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    </script>
</body>
</html>