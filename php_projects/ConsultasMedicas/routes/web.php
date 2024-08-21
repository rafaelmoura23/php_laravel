<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Models\Vaga;
use App\Http\Controllers\VagaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\MedicoController;
use App\Http\Middleware\AgendamentoMiddleware;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\HorarioController;
use App\Http\Middleware\VagaMiddleware;
use App\Models\Agendamento;
use App\Models\Inscricao;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rota para exibir o formulário de login
Route::get('/login', [UsuarioController::class, 'showLoginForm'])->name('login');


// Rota para processar o login
Route::post('/login', [UsuarioController::class, 'login'])->name('usuarios.login');


// Rota para exibir o formulário de registro
Route::get('/registro', [UsuarioController::class, 'showRegistroForm'])->name('usuarios.registro');


// Rota para processar o registro
Route::post('/registro', [UsuarioController::class, 'registro'])->name('usuarios.registro');


// Rota para logout
Route::post('/logout', [UsuarioController::class, 'logout'])->name('usuarios.logout');

// Rota para o dashboard, protegida por autenticação
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Rota para agendamentos
Route::resource('/agendamentos', AgendamentoController::class)->middleware(AgendamentoMiddleware::class)->except('show');

// Rota para lista dos médicos cadastrados
Route::get('/medicos', [UsuarioController::class, 'listarMedicos'])->name('medicos.index');

// Rota para ver os horários dos médicos
Route::get('usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');


Route::get('/consulta/create', [ConsultaController::class, 'create'])->name('consulta.create');


Route::post('/consulta/store', [ConsultaController::class, 'store'])->name('consulta.store');
