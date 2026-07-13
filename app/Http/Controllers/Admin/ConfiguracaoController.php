<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracaoController extends Controller
{
    /**
     * Exibe o formulário de configuração das imagens.
     */
    public function editLanding()
    {
        // Busca o primeiro registro ou retorna uma instância vazia
        $configuracoes = Configuracao::first() ?? new Configuracao();

        return view('admin.configuracao', compact('configuracoes'));
    }

    /**
     * Processa o upload e atualiza as fotos no banco de dados.
     */
    public function updateLanding(Request $request)
    {
        // 1. Validação dos arquivos de imagem (máximo 2MB por foto)
        $request->validate([
            'foto_hero' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'foto_espaco' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'image' => 'O arquivo selecionado deve ser uma imagem válida.',
            'mimes' => 'A imagem deve estar nos formatos: jpeg, png, jpg ou webp.',
            'max' => 'A imagem não pode ser maior do que 2MB.',
        ]);

        // 2. Busca ou cria o registro único de configurações
        $config = Configuracao::first() ?? new Configuracao();

        // 3. Processa a Foto Inicial (Hero)
        if ($request->hasFile('foto_hero')) {
            // Deleta a foto anterior se ela existir no storage
            if ($config->foto_hero && Storage::disk('public')->exists($config->foto_hero)) {
                Storage::disk('public')->delete($config->foto_hero);
            }
            // Salva a nova foto na pasta 'public/landing'
            $config->foto_hero = $request->file('foto_hero')->store('landing', 'public');
        }

        // 4. Processa a Foto do Espaço
        if ($request->hasFile('foto_espaco')) {
            // Deleta a foto anterior se ela existir no storage
            if ($config->foto_espaco && Storage::disk('public')->exists($config->foto_espaco)) {
                Storage::disk('public')->delete($config->foto_espaco);
            }
            // Salva a nova foto na pasta 'public/landing'
            $config->foto_espaco = $request->file('foto_espaco')->store('landing', 'public');
        }

        // 5. Salva as alterações no banco de dados
        $config->save();

        // 6. Redireciona de volta com a mensagem de sucesso que o SweetAlert2 vai ler
        return redirect()->back()->with('sucesso', 'As imagens da Landing Page foram atualizadas com sucesso!');
    }
}