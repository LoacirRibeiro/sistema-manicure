<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROTAS PÚBLICAS (Acessíveis por qualquer visitante)
// ==========================================

// Página Inicial e Portfólio
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/portfolio', [PortfolioController::class, 'portfolio'])->name('portfolio');

// Autenticação: Login
Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
Route::post('/login', [AuthController::class, 'efetuarLogin']);

// Autenticação: Cadastro
Route::get('/cadastro', [AuthController::class, 'mostrarCadastro'])->name('cadastro');
Route::post('/cadastro', [AuthController::class, 'efetuarCadastro']);

// Autenticação: Recuperação de Senha
Route::get('/esqueceu-senha', [AuthController::class, 'mostrarEsqueceuSenha'])->name('password.request');
Route::post('/esqueceu-senha', [AuthController::class, 'enviarLinkRecuperacao'])->name('password.email');


// ==========================================
// ROTAS PROTEGIDAS (Apenas usuários logados)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Fluxo de Agendamento do Cliente
    Route::get('/agendamento/horarios', [AgendamentoController::class, 'escolherHorario'])->name('agendamento.horarios');
    Route::post('/agendamento/salvar', [AgendamentoController::class, 'salvarAgendamento'])->name('agendamento.salvar');
    Route::get('/meus-agendamentos', [AgendamentoController::class, 'meusAgendamentos'])->name('cliente.agendamentos');

});


// ==========================================
// ROTAS ADMINISTRATIVAS (Apenas Admin logado)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
    // Painel Principal
    Route::get('/painel', [AdminController::class, 'index'])->name('admin.painel');
    
    // Ações de Agendamentos
    Route::post('/agendamento/{id}/concluir', [AdminController::class, 'concluir'])->name('admin.agendamento.concluir');
    Route::post('/agendamento/{id}/cancelar', [AdminController::class, 'cancelar'])->name('admin.agendamento.cancelar');
    Route::post('/agendamento/{id}/remarcar', [AdminController::class, 'processarRemarcacao'])->name('admin.agendamento.processarRemarcacao');

    // Faturamento Financeiro
    Route::get('/faturamento', [AdminController::class, 'faturamento'])->name('admin.faturamento');

    // CRUD de Despesas
    Route::get('/despesas', [AdminController::class, 'listarDespesas'])->name('admin.despesas.index');
    Route::post('/despesas', [AdminController::class, 'salvarDespesa'])->name('admin.despesas.store');
    Route::put('/despesas/{id}', [AdminController::class, 'atualizarDespesa'])->name('admin.despesas.update');
    Route::delete('/despesas/{id}', [AdminController::class, 'deletarDespesa'])->name('admin.despesas.destroy');

    // CRUD de Serviços
    Route::get('/servicos', [AdminController::class, 'listarServicos'])->name('admin.servicos.index');
    Route::post('/servicos', [AdminController::class, 'salvarServico'])->name('admin.servicos.store');
    Route::put('/servicos/{id}', [AdminController::class, 'atualizarServico'])->name('admin.servicos.update');
    Route::delete('/servicos/{id}', [AdminController::class, 'deletarServico'])->name('admin.servicos.destroy');

    // Certifique-se de que estão dentro do grupo admin ou adicione o prefixo manualmente:
    Route::get('/configuracao', [ConfiguracaoController::class, 'editLanding'])->name('admin.landing.edit');
    Route::put('/configuracao', [ConfiguracaoController::class, 'updateLanding'])->name('admin.landing.update');

});