<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.5); }
        .bg-neon { background-color: #FF007F; box-shadow: 0 0 20px rgba(255, 0, 127, 0.4); }
        .card-glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full card-glass p-8 rounded-3xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-pink-600 rounded-full filter blur-[80px] opacity-20"></div>
        
        <h2 class="text-3xl font-bold tracking-tighter text-center uppercase mb-2">Recuperar<span class="text-neon">Senha</span></h2>
        <p class="text-zinc-400 text-sm text-center mb-8">Digite seu e-mail cadastrado para redefinir sua senha.</p>

        {{-- Bloco para Exibição de Sucesso Backend (Laravel) --}}
        @if(session('status'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Bloco para Exibição de Erros Backend (Laravel) --}}
        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Container para Erros Dinâmicos de Validação do Frontend (JS) --}}
        <div id="erro-container" class="hidden mb-4 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-sm font-medium"></div>

        <form id="formRecuperar" action="{{ route('password.email') }}" method="POST" class="space-y-5" novalidate>
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-2">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-neon transition">
            </div>
            <button type="submit" class="w-full bg-neon text-white font-bold py-4 rounded-xl transition transform hover:scale-[1.02] uppercase tracking-widest text-sm">Enviar Link</button>
        </form>
        
        <p class="text-zinc-500 text-sm text-center mt-6"><a href="{{ route('login') }}" class="text-neon hover:underline">&larr; Voltar para o Login</a></p>
    </div>

    <script>
        // --- VALIDAÇÃO ANTES DO ENVIO (Inline Alert) ---
        document.getElementById('formRecuperar').addEventListener('submit', function(e) {
            const erroContainer = document.getElementById('erro-container');
            
            // Limpa mensagens anteriores
            erroContainer.classList.add('hidden');
            erroContainer.innerText = '';

            const email = document.getElementById('email').value.trim();
            let mensagemErro = '';

            // Verifica se o campo está vazio
            if (!email) {
                mensagemErro = 'Por favor, insira o seu endereço de e-mail.';
            } 
            // Verifica a estrutura básica do e-mail
            else if (!linearEmailValid(email)) {
                mensagemErro = 'Por favor, insira um endereço de e-mail válido (ex: nome@email.com).';
            }

            // Caso encontre erro, bloqueia o envio e renderiza a mensagem
            if (mensagemErro !== '') {
                e.preventDefault();
                erroContainer.innerText = mensagemErro;
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