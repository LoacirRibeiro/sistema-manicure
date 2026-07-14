<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha seu Horário - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .font-title { font-family: 'Playfair Display', serif; }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.5); }
        .bg-neon { background-color: #FF007F; box-shadow: 0 0 20px rgba(255, 0, 127, 0.4); }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex flex-col justify-between">

    {{-- Header --}}
    <header class="p-6 flex justify-between items-center border-b border-zinc-900">
        <a href="#" class="text-2xl font-black tracking-tighter uppercase">Nails<span class="text-neon">Studio</span></a>
        
        @if(request('remarcar_id'))
            <a href="{{ route('admin.painel') }}" class="text-pink-400 hover:text-pink-300 transition text-sm font-semibold uppercase tracking-widest">Voltar ao Painel</a>
        @else
            <a href="{{ route('home.index') }}" class="text-zinc-400 hover:text-white transition text-sm font-semibold uppercase tracking-widest"> Voltar</a>
        @endif
    </header>

    {{-- Conteúdo Principal --}}
    <main class="py-12 px-4 flex-grow flex items-center justify-center">
        <div class="max-w-4xl w-full card-glass p-6 md:p-10 rounded-3xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-pink-600 rounded-full filter blur-[90px] opacity-15"></div>
            
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="text-neon font-bold tracking-widest uppercase text-xs">
                        {{ request('remarcar_id') ? 'Modo Administrativo' : 'Agendamento Online' }}
                    </span>
                    <h2 class="text-3xl font-title mt-2">
                        {{ request('remarcar_id') ? 'Remarcar Atendimento da Cliente' : 'Agende seu horário' }}
                    </h2>
                    <p class="text-sm text-zinc-400 mt-2 max-w-xl">
                        Defina o serviço, escolha a profissional de sua preferência e selecione o melhor dia e horário para o seu atendimento.
                    </p>
                </div>
                <div class="text-right text-xs text-zinc-500">
                    Cliente: <span class="text-zinc-300 font-bold block">{{ request('cliente_nome') ?? auth()->user()->name ?? 'Visitante' }}</span>
                </div>
            </div>

            <form action="{{ route('agendamento.salvar') }}" method="POST" class="space-y-8">
                @csrf
                
                <input type="hidden" name="cliente_nome" value="{{ request('cliente_nome') ?? auth()->user()->name ?? '' }}">
                <input type="hidden" name="cliente_whatsapp" value="{{ request('cliente_whatsapp') ?? auth()->user()->telefone ?? '' }}">

                @if(request('remarcar_id'))
                    <input type="hidden" name="remarcar_id" value="{{ request('remarcar_id') }}">
                    <input type="hidden" name="servico_id_hidden" value="{{ request('servico_id') }}">
                    <input type="hidden" name="manicure_id_hidden" value="{{ request('manicure_id') }}">
                @endif

                {{-- PASSO: ESCOLHA DO SERVIÇO E DA MANICURE --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-2">Procedimento Desejado</label>
                        <select name="servico_id" required class="w-full bg-zinc-900/60 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-neon transition text-sm">
                            <option value="" disabled selected>Selecione o que deseja fazer...</option>
                            @foreach($servicos as $servico)
                                <option value="{{ $servico->id }}" {{ (request('servico_id') == $servico->id || old('servico_id') == $servico->id) ? 'selected' : '' }}>
                                    {{ $servico->nome }} — R$ {{ number_format($servico->preco, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400 mb-2">Escolha a Profissional</label>
                        <select name="manicure_id" required class="w-full bg-zinc-900/60 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-neon transition text-sm">
                            <option value="" disabled selected>Escolha uma manicure...</option>
                            @foreach($manicures as $manicure)
                                <option value="{{ $manicure->id }}" {{ (request('manicure_id') == $manicure->id || old('manicure_id') == $manicure->id) ? 'selected' : '' }}>
                                    {{ $manicure->name }} 
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- CALENDÁRIO COM APARÊNCIA ALINHADA --}}
                <div class="space-y-3">
                    <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400">Selecione o Dia desejado</label>
                    <div class="grid grid-cols-7 gap-2">
                        @php
                            // Tradução otimizada carregada fora do loop
                            $diasSemanaPtBr = [
                                'Sun' => 'Dom', 'Mon' => 'Seg', 'Tue' => 'Ter', 
                                'Wed' => 'Qua', 'Thu' => 'Qui', 'Fri' => 'Sex', 'Sat' => 'Sáb'
                            ];
                        @endphp

                        @for($i = 0; $i < 14; $i++)
                            @php
                                $dataBotao = \Carbon\Carbon::today()->addDays($i);
                                $stringData = $dataBotao->format('Y-m-d');
                                
                                $diaSemana = $diasSemanaPtBr[$dataBotao->format('D')];
                                $diaMes = $dataBotao->format('d');
                                
                                $isCheck = ($stringData === $dataSelecionada);
                                $isLotado = in_array($stringData, $diasLotados ?? []);
                                $isDomingo = $dataBotao->isSunday();
                            @endphp
                            
                            <label class="cursor-pointer {{ $isDomingo ? 'pointer-events-none' : '' }}">
                                <input type="radio" name="data_escolhida" value="{{ $stringData }}" class="hidden peer" 
                                    {{ $isCheck ? 'checked' : '' }}
                                    {{ ($isLotado || $isDomingo) ? 'disabled' : '' }}
                                    onchange="this.form.action='{{ route('agendamento.horarios') }}'; this.form.method='GET'; this.form.submit();">
                                
                                <div class="flex flex-col items-center justify-center py-2.5 rounded-xl border transition-all 
                                    {{ $isCheck ? 'bg-zinc-900 border-pink-500 text-neon font-semibold' : 'bg-zinc-900/40 hover:border-zinc-700' }}
                                    @if(!$isCheck)
                                        @if($isDomingo)
                                            border-zinc-800 text-zinc-600 opacity-60
                                        @elseif($isLotado)
                                            border-red-950/80 text-red-400/90 hover:bg-red-950/10
                                        @else
                                            border-emerald-950/80 text-emerald-400/90 hover:bg-emerald-950/10
                                        @endif
                                    @endif
                                ">
                                    <span class="text-[9px] uppercase tracking-wider font-semibold 
                                        @if(!$isCheck)
                                            {{ $isDomingo ? 'text-zinc-600' : ($isLotado ? 'text-red-500/70' : 'text-emerald-500/70') }}
                                        @endif
                                    ">
                                        {{ $diaSemana }}
                                    </span>

                                    <span class="text-base font-bold mt-0.5">{{ $diaMes }}</span>

                                    <span class="text-[7px] uppercase font-bold tracking-tighter mt-0.5">
                                        @if($isDomingo)
                                            Fechado
                                        @elseif($isLotado)
                                            <span class="text-red-500">● Lotado</span>
                                        @else
                                            <span class="text-emerald-500">● Vagas</span>
                                        @endif
                                    </span>
                                </div>
                            </label>
                        @endfor
                    </div>
                </div>

                {{-- HORÁRIOS --}}
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-zinc-800 pb-2">
                        <label class="block text-xs uppercase tracking-widest font-semibold text-zinc-400">Horários Disponíveis para este dia</label>
                        <span class="text-xs text-zinc-500 font-medium">
                            Data focada: <span class="text-zinc-300 font-bold">{{ \Carbon\Carbon::parse($dataSelecionada)->format('d/m/Y') }}</span>
                        </span>
                    </div>
                    
                    @if($estudioFechado)
                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 text-center text-zinc-400 text-sm">
                            <i class="la la-clock text-xl text-pink-500 mr-1"></i> Não atendemos aos domingos. Por favor, escolha outro dia na grade acima!
                        </div>
                    @else
                        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                            @forelse($horariosDisponiveis as $h)
                                @php
                                    $horaFormatada = \Carbon\Carbon::parse($h->hora)->format('H:i');
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio" name="hora_escolhida" value="{{ $horaFormatada }}" required class="hidden peer" {{ old('hora_escolhida') == $horaFormatada ? 'checked' : '' }}>
                                    <div class="text-center py-2.5 rounded-xl border bg-zinc-900/20 border-zinc-800/80 text-zinc-300 text-xs font-bold transition-all peer-checked:bg-pink-500 peer-checked:border-pink-500 peer-checked:text-white hover:border-zinc-700">
                                        {{ $horaFormatada }}
                                    </div>
                                </label>
                            @empty
                                <div class="col-span-full text-center py-8 bg-zinc-900/30 border border-zinc-800 rounded-xl text-xs text-zinc-500">
                                    Nenhum horário disponível para os procedimentos selecionados neste dia.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>

                {{-- Botão de Confirmação --}}
                <div class="pt-4 border-t border-zinc-900">
                    <button type="submit" class="w-full bg-neon text-white font-bold py-4 rounded-xl transition transform hover:scale-[1.01] active:scale-[0.99] uppercase tracking-widest text-center text-sm">
                        {{ request('remarcar_id') ? 'Salvar Nova Data & Horário' : 'Confirmar Agendamento' }}
                    </button>
                </div>
            </form>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-6 border-t border-zinc-900 text-center text-zinc-600 text-xs">
        <p>&copy; {{ date('Y') }} NailsStudio Design. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('erro'))
            Swal.fire({
                title: 'Erro de Autenticação',
                text: "{{ session('erro') }}",
                icon: 'error',
                background: '#121214',
                color: '#e4e4e7',
                confirmButtonColor: '#ef4444'
            });
        @endif

        const urlParams = new URLSearchParams(window.location.search);
        const inputRemarcar = document.querySelector('input[name="remarcar_id"]');
        const remarcarId = urlParams.get('remarcar_id') || (inputRemarcar ? inputRemarcar.value : null);

        if (remarcarId) {
            const form = document.querySelector('form');
            
            if (form) {
                form.querySelectorAll('input[name="data_escolhida"]').forEach(radio => {
                    radio.setAttribute('onchange', ''); 
                    radio.addEventListener('change', function() {
                        let clienteNome = form.querySelector('input[name="cliente_nome"]').value;
                        let clienteWhats = form.querySelector('input[name="cliente_whatsapp"]').value;
                        let servicoId = form.querySelector('select[name="servico_id"]').value || urlParams.get('servico_id');
                        let manicureId = form.querySelector('select[name="manicure_id"]').value || urlParams.get('manicure_id');

                        window.location.href = `{{ route('agendamento.horarios') }}?data_escolhida=${this.value}&cliente_nome=${encodeURIComponent(clienteNome)}&cliente_whatsapp=${encodeURIComponent(clienteWhats)}&servico_id=${servicoId}&manicure_id=${manicureId}&remarcar_id=${remarcarId}`;
                    });
                });

                form.addEventListener('submit', function(event) {
                    event.preventDefault(); 
                    
                    const dataSelecionadaRadio = form.querySelector('input[name="data_chosen_admin"]:checked') || form.querySelector('input[name="data_escolhida"]:checked');
                    const horaSelecionadaRadio = form.querySelector('input[name="hora_escolhida"]:checked');

                    if (!dataSelecionadaRadio || !horaSelecionadaRadio) {
                        Swal.fire({
                            title: 'Atenção',
                            text: 'Selecione o novo horário disponível antes de prosseguir.',
                            icon: 'warning',
                            background: '#121214',
                            color: '#e4e4e7',
                            confirmButtonColor: '#FF007F'
                        });
                        return;
                    }

                    const novaData = dataSelecionadaRadio.value;
                    const novaHora = horaSelecionadaRadio.value;

                    Swal.fire({
                        title: 'Confirmação Master',
                        text: 'Insira sua senha de Administrador para efetivar a remarcação:',
                        input: 'password',
                        inputPlaceholder: 'Sua senha admin',
                        showCancelButton: true,
                        background: '#121214',
                        color: '#e4e4e7',
                        confirmButtonColor: '#FF007F',
                        cancelButtonColor: '#27272a',
                        confirmButtonText: 'Confirmar Alteração',
                        cancelButtonText: 'Cancelar',
                        customClass: { popup: 'border border-zinc-800 rounded-3xl' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (!result.value) {
                                Swal.fire({ text: 'A senha é obrigatória!', icon: 'error', background: '#121214', color: '#e4e4e7' });
                                return;
                            }

                            form.action = `/admin/agendamento/${remarcarId}/remarcar`;
                            form.method = 'POST';
                            
                            const inputPassword = document.createElement('input');
                            inputPassword.type = 'hidden';
                            inputPassword.name = 'admin_password';
                            inputPassword.value = result.value;
                            
                            const inputNovaData = document.createElement('input');
                            inputNovaData.type = 'hidden';
                            inputNovaData.name = 'nova_data';
                            inputNovaData.value = novaData;

                            const inputNovaHora = document.createElement('input');
                            inputNovaHora.type = 'hidden';
                            inputNovaHora.name = 'nova_hora';
                            inputNovaHora.value = novaHora;

                            form.appendChild(inputPassword);
                            form.appendChild(inputNovaData);
                            form.appendChild(inputNovaHora);

                            form.submit(); 
                        }
                    });
                });
            }
        }
    </script>
</body>
</html>