<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
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
        
        <h2 class="text-3xl font-bold tracking-tighter text-center uppercase mb-6">Criar<span class="text-neon">Conta</span></h2>

        {{-- Bloco para Exibição de Mensagens Backend (Laravel) --}}
        @if ($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs space-y-1">
                <strong>Verifique os seguintes campos:</strong>
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

        <form id="formCadastro" action="{{ route('cadastro') }}" method="POST" class="space-y-4" novalidate>
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-1">Nome Completo</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-neon transition">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-1">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-neon transition">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-1">Telefone / WhatsApp</label>
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 99999-0000" maxlength="15" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-neon transition">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-1">Senha</label>
                <input type="password" id="password" name="password" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-neon transition">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-1">Confirme a Senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-neon transition">
            </div>
            <button type="submit" class="w-full bg-neon text-white font-bold py-3.5 rounded-xl transition transform hover:scale-[1.02] uppercase tracking-widest text-sm mt-2">Cadastrar</button>
        </form>
        
        <p class="text-zinc-500 text-sm text-center mt-4">Já tem conta? <a href="{{ route('login') }}" class="text-neon hover:underline">Faça Login</a></p>
    </div>

    <script>
        // --- 1. MÁSCARA EM TEMPO REAL PARA O NOME (Capitalizar Primeiras Letras) ---
        const inputName = document.getElementById('name');
        inputName.addEventListener('input', (e) => {
            let texto = e.target.value;
            let textoCapitalizado = texto.replace(/(^\w|\s\w)/g, function(letra) {
                return letra.toUpperCase();
            });
            e.target.value = textoCapitalizado;
        });

        // --- 2. MÁSCARA EM TEMPO REAL PARA O TELEFONE ---
        const inputTelefone = document.getElementById('telefone');
        inputTelefone.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 6) {
                value = `(${value.slice(0, 2)}) ${value.slice(2, 7)}-${value.slice(7)}`;
            } else if (value.length > 2) {
                value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
            } else if (value.length > 0) {
                value = `(${value}`;
            }
            e.target.value = value;
        });

        // --- 3. VALIDAÇÃO ANTES DO ENVIO (Inline Alert) ---
        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            const erroContainer = document.getElementById('erro-container');
            
            // Força a limpeza visual inicial
            erroContainer.classList.add('hidden');
            erroContainer.innerText = '';

            const nomeInput = document.getElementById('name');
            nomeInput.value = nomeInput.value.replace(/\s+/g, ' ').trim();
            const nome = nomeInput.value;
            
            const email = document.getElementById('email').value.trim();
            const telefone = inputTelefone.value.replace(/\D/g, '');
            const senha = document.getElementById('password').value;
            const confirmacao = document.getElementById('password_confirmation').value;

            let mensagemErro = '';

            // Validação de Campos Vazios Básica (Contorna bugs do required nativo)
            if (!nome || !email || !telefone || !senha || !confirmacao) {
                mensagemErro = 'Por favor, preencha todos os campos do formulário.';
            }
            // Validação de Nome Completo
            else if (!nome.includes(' ') || nome.split(' ').filter(p => p.length > 0).length < 2) {
                mensagemErro = 'Por favor, insira o seu nome e sobrenome completo.';
            }
            // Validação simples de formato de e-mail
            else if (!linearEmailValid(email)) {
                mensagemErro = 'Por favor, insira um endereço de e-mail válido.';
            }
            // Validação do Telefone
            else if (telefone.length < 11) {
                mensagemErro = 'O número de telefone deve conter o DDD mais os 9 dígitos (ex: (11) 99999-9999).';
            }
            // Comparação das Senhas
            else if (senha !== confirmacao) {
                mensagemErro = 'A confirmação de senha não coincide com a senha informada.';
            }

            // Se encontrou algum erro, interrompe o envio imediatamente e exibe a mensagem
            if (mensagemErro !== '') {
                e.preventDefault();
                erroContainer.innerText = mensagemErro;
                erroContainer.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        // Função auxiliar para validar e-mail sem travar o submit nativo
        function linearEmailValid(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    </script>
</body>
</html>