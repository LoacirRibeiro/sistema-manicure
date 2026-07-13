<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despesas - NailsStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #0f0f0f; }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-neon { color: #FF007F; text-shadow: 0 0 10px rgba(255, 0, 127, 0.3); }
    </style>
</head>
<body class="text-zinc-200 min-h-screen flex flex-col justify-between">

    <header class="p-6 bg-zinc-950/80 border-b border-zinc-900 flex justify-between items-center backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <span class="bg-red-500/10 text-red-400 font-bold text-xs px-3 py-1 rounded-full uppercase tracking-widest border border-red-500/20">Saídas</span>
            <a href="{{ route('admin.painel') }}" class="text-xl font-black tracking-tighter uppercase">Nails<span class="text-neon">Studio</span></a>
        </div>
        <a href="{{ route('admin.painel') }}" class="text-xs font-semibold uppercase tracking-wider bg-zinc-900 border border-zinc-800 px-4 py-2 rounded-xl hover:bg-zinc-800 transition">Voltar ao Painel</a>
    </header>

    <main class="flex-grow p-4 md:p-10 max-w-6xl w-full mx-auto space-y-8">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-zinc-900 pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Fluxo de Despesas</h1>
                <p class="text-sm text-zinc-500 mt-1">Gerencie os custos fixos e variáveis do salão.</p>
            </div>
            
            <form action="{{ route('admin.despesas.index') }}" method="GET" class="flex items-center gap-2 bg-zinc-900/50 p-1.5 rounded-xl border border-zinc-800/80">
                <div class="flex items-center gap-2 pl-2">
                    <i class="la la-filter text-zinc-400 text-sm"></i>
                    <span class="text-xs uppercase tracking-wider text-zinc-400 font-medium hidden md:inline">Período:</span>
                </div>
                
                <select name="mes" onchange="this.form.submit()" class="bg-zinc-950 border border-zinc-800 text-white rounded-lg p-2 text-xs focus:border-pink-500 focus:outline-none cursor-pointer">
                    @foreach($mesesComDados as $opcao)
                        @php
                            $mesesPtBr = [
                                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                                '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                                '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                            ];
                            $nomeMes = $mesesPtBr[$opcao->numero_mes] ?? 'Invalido';
                        @endphp
                        <option value="{{ $opcao->mes_ano }}" {{ $mesAno == $opcao->mes_ano ? 'selected' : '' }}>
                            {{ $nomeMes }} de {{ $opcao->ano }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Formulário de Cadastro --}}
            <div class="card-glass p-6 rounded-3xl h-fit space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-400"><i class="la la-plus-circle text-neon"></i> Novo Lançamento</h3>
                
                <form id="formNovaDespesa" action="{{ route('admin.despesas.store') }}" method="POST" onsubmit="prepararEnvioData(event, 'input_data_br', 'hidden_data_vencimento')" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs text-zinc-400 block mb-1">Descrição / Fornecedor</label>
                        <input type="text" name="descricao" placeholder="Ex: Conta de Energia" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-2.5 text-sm text-white focus:border-pink-500 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="text-xs text-zinc-400 block mb-1">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" placeholder="0,00" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-2.5 text-sm text-white focus:border-pink-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="text-xs text-zinc-400 block mb-1">Data do Gasto/Vencimento (DD/MM/AAAA)</label>
                        <div class="relative">
                            <input type="text" id="input_data_br" placeholder="Ex: 10/05/2026" maxlength="10" oninput="mascaraData(this)" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-2.5 text-sm text-white focus:border-pink-500 focus:outline-none">
                            <input type="hidden" id="hidden_data_vencimento" name="data_vencimento">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-zinc-400 block mb-1">Categoria</label>
                        <select name="categoria" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-2.5 text-sm text-white focus:border-pink-500 focus:outline-none">
                            <option value="Salao">Estrutura (Aluguel/Manutenção)</option>
                            <option value="Utilidades">Utilidades (Água, Luz, Internet)</option>
                            <option value="Produtos">Produtos e Insumos (Esmaltes, lixas, etc.)</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-sm transition mt-2">Registrar Saída</button>
                </form>
            </div>

            {{-- Listagem de Custos --}}
            <div class="md:col-span-2 card-glass p-6 rounded-3xl space-y-4">
                <div class="flex justify-between items-center border-b border-zinc-900 pb-2">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-400">
                        <i class="la la-list text-red-400"></i> Gastos de 
                        <span class="text-zinc-200 lowercase">
                            {{ \Carbon\Carbon::parse($mesAno . '-01')->translatedFormat('F/Y') }}
                        </span>
                    </h3>
                    <span class="text-sm font-bold text-red-400">Total: R$ {{ number_format($totalDespesas, 2, ',', '.') }}</span>
                </div>

                <div class="overflow-y-auto max-h-[400px] space-y-3 pr-1">
                    @forelse($despesas as $despesa)
                        <div class="flex justify-between items-center bg-zinc-900/50 border border-zinc-900 p-4 rounded-2xl hover:border-zinc-800 transition">
                            <div>
                                <h4 class="text-sm font-semibold text-white">{{ $despesa->descricao }}</h4>
                                <div class="flex gap-2 items-center mt-1">
                                    <span class="text-[10px] bg-zinc-800 px-2 py-0.5 rounded text-zinc-400">{{ $despesa->categoria }}</span>
                                    <span class="text-[11px] text-zinc-500"><i class="la la-calendar"></i> {{ \Carbon\Carbon::parse($despesa->data_vencimento)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-bold text-red-400">- R$ {{ number_format($despesa->valor, 2, ',', '.') }}</span>
                                
                                <div class="flex items-center gap-2">
                                    {{-- Botão de Editar (Abre o Modal via JS) --}}
                                    <button type="button" 
                                            onclick="abrirModalEdicao({{ json_encode($despesa) }}, '{{ \Carbon\Carbon::parse($despesa->data_vencimento)->format('d/m/Y') }}')" 
                                            class="text-zinc-500 hover:text-blue-400 transition text-lg">
                                        <i class="la la-edit"></i>
                                    </button>

                                    {{-- Botão de Excluir --}}
                                    <form action="{{ route('admin.despesas.destroy', $despesa->id) }}" method="POST" onsubmit="return confirm('Excluir esta despesa?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-zinc-600 hover:text-red-500 transition text-lg"><i class="la la-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-500 text-xs italic text-center py-8">Nenhuma despesa lançada para este período.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    {{-- MODAL DE EDIÇÃO OCULTO --}}
    <div id="modalEdicao" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="card-glass max-w-md w-full bg-zinc-950 p-6 rounded-3xl space-y-4 border border-zinc-800">
            <div class="flex justify-between items-center border-b border-zinc-900 pb-2">
                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-300"><i class="la la-edit text-pink-500"></i> Editar Despesa</h3>
                <button onclick="fecharModalEdicao()" class="text-zinc-500 hover:text-white transition text-xl">&times;</button>
            </div>

            <form id="formEditarDespesa" method="POST" onsubmit="prepararEnvioData(event, 'edit_data_br', 'edit_hidden_data_vencimento')" class="space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="text-xs text-zinc-400 block mb-1">Descrição / Fornecedor</label>
                    <input type="text" id="edit_descricao" name="descricao" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-2.5 text-sm text-white focus:border-pink-500 focus:outline-none">
                </div>
                
                <div>
                    <label class="text-xs text-zinc-400 block mb-1">Valor (R$)</label>
                    <input type="number" step="0.01" id="edit_valor" name="valor" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-2.5 text-sm text-white focus:border-pink-500 focus:outline-none">
                </div>

                <div>
                    <label class="text-xs text-zinc-400 block mb-1">Data do Gasto/Vencimento (DD/MM/AAAA)</label>
                    <div class="relative">
                        <input type="text" id="edit_data_br" maxlength="10" oninput="mascaraData(this)" required class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-2.5 text-sm text-white focus:border-pink-500 focus:outline-none">
                        <input type="hidden" id="edit_hidden_data_vencimento" name="data_vencimento">
                    </div>
                </div>

                <div>
                    <label class="text-xs text-zinc-400 block mb-1">Categoria</label>
                    <select id="edit_categoria" name="categoria" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl p-2.5 text-sm text-white focus:border-pink-500 focus:outline-none">
                        <option value="Salao">Estrutura (Aluguel/Manutenção)</option>
                        <option value="Utilidades">Utilidades (Água, Luz, Internet)</option>
                        <option value="Produtos">Produtos e Insumos (Esmaltes, lixas, etc.)</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="fecharModalEdicao()" class="w-1/3 bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-2.5 rounded-xl text-sm transition">Cancelar</button>
                    <button type="submit" class="w-2/3 bg-pink-600 hover:bg-pink-700 text-white font-bold py-2.5 rounded-xl text-sm transition">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function mascaraData(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 8);
            if (v.length >= 5) {
                input.value = `${v.slice(0, 2)}/${v.slice(2, 4)}/${v.slice(4)}`;
            } else if (v.length >= 3) {
                input.value = `${v.slice(0, 2)}/${v.slice(2)}`;
            } else {
                input.value = v;
            }
        }

        function prepararEnvioData(event, idInputBr, idHidden) {
            const inputBr = document.getElementById(idInputBr).value;
            const hiddenInput = document.getElementById(idHidden);
            
            if (inputBr.length === 10) {
                const partes = inputBr.split('/');
                if (partes.length === 3) {
                    hiddenInput.value = `${partes[2]}-${partes[1]}-${partes[0]}`;
                    return true;
                }
            }
            
            event.preventDefault();
            alert('Por favor, digite uma data válida no formato DD/MM/AAAA');
            return false;
        }

        // Funções para Controle do Modal de Edição
        function abrirModalEdicao(despesa, dataFormatada) {
            document.getElementById('edit_descricao').value = despesa.descricao;
            document.getElementById('edit_valor').value = despesa.valor;
            document.getElementById('edit_data_br').value = dataFormatada;
            document.getElementById('edit_categoria').value = despesa.categoria;
            
            // Define dinamicamente a URL da rota de atualização usando a ID da despesa
            document.getElementById('formEditarDespesa').action = `/admin/despesas/${despesa.id}`;
            
            document.getElementById('modalEdicao').classList.remove('hidden');
        }

        function fecharModalEdicao() {
            document.getElementById('modalEdicao').classList.add('hidden');
        }
    </script>
</body>
</html>