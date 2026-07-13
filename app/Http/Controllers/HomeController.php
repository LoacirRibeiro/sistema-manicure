<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use App\Models\User;
use App\Models\Horario;
use App\Models\Configuracao;
use App\Models\Agendamento;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    public function index()
{
    $agendamentoAtivo = null;

    if (auth()->check()) {
        $agendamentoAtivo = Agendamento::where('cliente_nome', auth()->user()->name)
            // Esconde se estiver concluído ou cancelado usando os termos exatos do seu banco
            ->whereNotIn('status', ['concluido', 'cancelado']) 
            ->where('data_escolhida', '>=', Carbon::today()) 
            ->with(['servico', 'manicure'])
            ->orderBy('data_escolhida', 'asc')
            ->orderBy('hora_escolhida', 'asc') 
            ->first();
    }

    return view('home.index', [
        'servicos' => Servico::all(),
        'configuracoes' => Configuracao::first(),
        'agendamentoAtivo' => $agendamentoAtivo
    ]);
}
}