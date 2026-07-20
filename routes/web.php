<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/rodar-seeder-manicure', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return "Banco limpo, migrado e seeders rodados com sucesso!";
    } catch (\Exception $e) {
        return "Erro: " . $e->getMessage();
    }
});

Route::get('/instalar-banco-manicure', function () {
    try {
        // Limpa o banco e roda as migrations na nuvem
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
        
        // Alimenta o banco com os seus seeders (serviços, etc)
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        
        return "Banco de dados configurado e populado com sucesso no Railway!";
    } catch (\Exception $e) {
        return "Erro ao configurar o banco: " . $e->getMessage();
    }
});

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
// ROTAS PROTEGIDAS (Apenas usuários logados - Clientes e Admins)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Fluxo de Agendamento do Cliente
    Route::get('/agendamento/horarios', [AgendamentoController::class, 'escolherHorario'])->name('agendamento.horarios');
    Route::post('/agendamento/salvar', [AgendamentoController::class, 'salvarAgendamento'])->name('agendamento.salvar');
    Route::get('/meus-agendamentos', [AgendamentoController::class, 'meusAgendamentos'])->name('cliente.agendamentos');
    Route::put('/meus-agendamentos/{id}/cancelar', [AgendamentoController::class, 'clienteCancela'])->name('cliente.agendamentos.cancelar');

});


// ==========================================
// ROTAS ADMINISTRATIVAS (Apenas Admin logado)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
    // Painel Principal
    Route::get('/painel', [AdminController::class, 'index'])->name('admin.painel');
    
    // Ações de Agendamentos Administrativas
    Route::post('/agendamento/{id}/concluir', [AgendamentoController::class, 'concluir'])->name('admin.agendamento.concluir');
    Route::post('/agendamento/{id}/cancelar', [AgendamentoController::class, 'cancelar'])->name('admin.agendamento.cancelar');
    Route::post('/agendamento/{id}/remarcar', [AgendamentoController::class, 'processarRemarcacao'])->name('admin.agendamento.processarRemarcacao');

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

    // Configurações da Landing Page
    Route::get('/configuracao', [ConfiguracaoController::class, 'editLanding'])->name('admin.landing.edit');
    Route::put('/configuracao', [ConfiguracaoController::class, 'updateLanding'])->name('admin.landing.update');

    // Controle de Faltas e Relatórios
    Route::post('/agendamento/{id}/faltou', [AgendamentoController::class, 'marcarFalta'])->name('admin.agendamento.faltou');
    Route::get('/relatorio-mensal', [AdminController::class, 'relatorio'])->name('admin.relatorio');

    // Tela que lista as clientes suspensas e Ação de Desbloqueio
    Route::get('/clientes-suspensos', [AgendamentoController::class, 'clientesSuspensos'])->name('admin.clientes.suspensos');
    Route::post('/clientes/{id}/desbloquear', [AgendamentoController::class, 'desbloquearCliente'])->name('admin.clientes.desbloquear');

    // Gráficos Administrativos
    Route::get('/admin/graficos', [AgendamentoController::class, 'graficosMensais'])->name('admin.graficos');

    // Listagem e gestão de usuários
    Route::get('/usuarios', [AdminController::class, 'listarUsuarios'])->name('admin.usuarios.index');
    Route::get('/agendamento/criar-para-usuario/{userId}', [AgendamentoController::class, 'criarParaUsuario'])->name('admin.agendamento.criarParaUsuario');
});