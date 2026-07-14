<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Serviços - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="bg-[#0f0f0f] text-zinc-200 min-h-screen p-8">

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Gerenciar <span class="text-pink-500">Serviços</span></h1>
            <a href="{{ route('admin.painel') }}" class="text-sm text-zinc-400 hover:text-white">&larr; Voltar ao Painel</a>
        </div>

        {{-- Formulário de Cadastro --}}
        <div class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-2xl mb-10">
            <h2 class="text-lg font-semibold mb-4 text-zinc-300">Novo Procedimento</h2>
            
            <form action="{{ route('admin.servicos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Nome do Serviço</label>
                        <input type="text" name="nome" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-pink-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Preço (R$)</label>
                        <input type="number" step="0.01" name="preco" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-pink-500 text-sm" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Duração (Minutos)</label>
                        <input type="number" name="duracao_minutos" value="60" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-pink-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Foto do Procedimento</label>
                        <input type="file" name="foto_exemplo" accept="image/*" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-2 py-1.5 text-zinc-400 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-white hover:file:bg-zinc-700">
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Breve Descrição</label>
                    <textarea name="descricao" rows="2" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-pink-500 text-sm"></textarea>
                </div>

                <button type="submit" class="bg-pink-600 hover:bg-pink-500 text-white font-bold px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition">
                    Salvar Serviço
                </button>
            </form>
        </div>

        {{-- Tabela de Serviços --}}
        <div class="bg-zinc-900/20 border border-zinc-800 rounded-2xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 bg-zinc-900/50 text-xs uppercase tracking-wider text-zinc-400">
                        <th class="p-4 w-20">Foto</th>
                        <th class="p-4">Serviço</th>
                        <th class="p-4">Descrição</th>
                        <th class="p-4 w-28">Preço</th>
                        <th class="p-4 w-24 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900 text-sm">
                    @foreach($servicos as $s)
                    <tr class="hover:bg-zinc-900/20 transition">
                        <td class="p-4">
                            <div class="w-12 h-12 bg-zinc-950 rounded-lg overflow-hidden border border-zinc-800">
                                @if($s->foto_exemplo)
                                    <img src="{{ asset('img/' . $s->foto_exemplo) }}" class="w-full h-full object-cover"> 
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-zinc-700"><i class="la la-image text-xl"></i></div>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 font-semibold text-white">{{ $s->nome }}</td>
                        <td class="p-4 text-zinc-400 text-xs max-w-xs truncate">{{ $s->descricao ?? 'Sem descrição.' }}</td>
                        <td class="p-4 text-pink-400 font-bold">R$ {{ number_format($s->preco, 2, ',', '.') }}</td>
                        <td class="p-4 flex justify-center items-center gap-2 mt-2">
                            {{-- Botão Editar --}}
                            <button type="button" 
                                    onclick="abrirModalEditar({{ json_encode($s) }})"
                                    class="text-zinc-400 hover:text-blue-400 transition text-lg p-1">
                                <i class="la la-edit"></i>
                            </button>

                            {{-- Form Deletar (Agora controlado via ID único por JavaScript) --}}
                            <form id="form-deletar-{{ $s->id }}" action="{{ route('admin.servicos.destroy', $s->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            
                            <button type="button" onclick="confirmarExclusao({{ $s->id }}, '{{ $s->nome }}')" class="text-zinc-500 hover:text-red-400 transition text-lg p-1">
                                <i class="la la-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL PARA EDIÇÃO --}}
    <div id="modalEditar" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
        <div class="bg-[#121214] border border-zinc-800 w-full max-w-2xl rounded-3xl p-6 md:p-8 relative card-glass">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Editar <span class="text-pink-500">Procedimento</span></h3>
                <button type="button" onclick="fecharModalEditar()" class="text-zinc-400 hover:text-white text-2xl">&times;</button>
            </div>

            <form id="formEditarServico" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Nome do Serviço</label>
                        <input type="text" id="edit_nome" name="nome" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-pink-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Preço (R$)</label>
                        <input type="number" step="0.01" id="edit_preco" name="preco" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-pink-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Duração (Minutos)</label>
                        <input type="number" id="edit_duracao" name="duracao_minutos" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-pink-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Nova Foto (Opcional)</label>
                    <input type="file" name="foto_exemplo" accept="image/*" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-2 py-1.5 text-zinc-400 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-white hover:file:bg-zinc-700">
                </div>
                <div>
                    <label class="block text-xs text-zinc-400 mb-1 uppercase tracking-wider">Descrição</label>
                    <textarea id="edit_descricao" name="descricao" rows="3" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-pink-500 text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-900">
                    <button type="button" onclick="fecharModalEditar()" class="bg-zinc-800 text-zinc-300 hover:bg-zinc-700 font-bold px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider transition">Cancelar</button>
                    <button type="submit" class="bg-pink-600 hover:bg-pink-500 text-white font-bold px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition">Atualizar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT INTERATIVO --}}
    <script>
        function abrirModalEditar(servico) {
            document.getElementById('formEditarServico').action = /admin/servicos/${servico.id};
            document.getElementById('edit_nome').value = servico.nome;
            document.getElementById('edit_preco').value = servico.preco;
            document.getElementById('edit_duracao').value = servico.duracao_minutos;
            document.getElementById('edit_descricao').value = servico.descricao || '';
            document.getElementById('modalEditar').classList.remove('hidden');
        }

        function fecharModalEditar() {
            document.getElementById('modalEditar').classList.add('hidden');
        }

        // 🔥 SweetAlert2: Confirmação Estilizada para Exclusão
        function confirmarExclusao(id, nome) {
            Swal.fire({
                title: 'Tem certeza?',
                text: O serviço "${nome}" será excluído permanentemente.,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#db2777', /* Rosa do Tailwind (pink-600) */
                cancelButtonColor: '#27272a',  /* Zinc escuro */
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
                background: '#18181b',         /* Fundo Escuro */
                color: '#f4f4f5'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submete o formulário oculto correspondente
                    document.getElementById(form-deletar-${id}).submit();
                }
            });
        }

        // 🪄 SweetAlert2: Alerta de Notificação de Sucesso vinda do Laravel Session
        @if(session('sucesso'))
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: "{{ session('sucesso') }}",
                timer: 3000,
                showConfirmButton: false,
                background: '#18181b',
                color: '#f4f4f5',
                iconColor: '#10b981' /* Verde */
            });
        @endif
    </script>
</body>
</html>