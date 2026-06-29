<?php

use App\Controllers\HomeController;
use App\Controllers\OcorrenciaController;
use App\Controllers\DashboardController;
use Core\Routing\Route;

// ==========================================
// ROTAS DE APLICAÇÃO (WEB / HTML)
// ==========================================

Route::get('/ocorrencias', [OcorrenciaController::class, 'index'])->middleware('auth');
Route::post('/ocorrencias', [OcorrenciaController::class, 'store'])->middleware('auth');
Route::post('/ocorrencias/{id}/aprovar', [OcorrenciaController::class, 'approve'])->middleware('auth');
Route::post('/ocorrencias/{id}/rejeitar', [OcorrenciaController::class, 'reject'])->middleware('auth');

Route::get('/alunos', [\App\Controllers\AlunoController::class, 'index'])->middleware('auth');
Route::post('/secretaria/alunos', [\App\Controllers\AlunoController::class, 'store'])->middleware('auth');
Route::post('/secretaria/vincular-turma', [\App\Controllers\AlunoController::class, 'vincularTurma'])->middleware('auth');
Route::post('/secretaria/vincular-responsavel', [\App\Controllers\AlunoController::class, 'vincularResponsavel'])->middleware('auth');

Route::get('/turmas', [\App\Controllers\TurmaController::class, 'index'])->middleware('auth');
Route::post('/secretaria/turmas', [\App\Controllers\TurmaController::class, 'store'])->middleware('auth');
Route::post('/secretaria/usuarios', [DashboardController::class, 'storeUsuario'])->middleware('auth');

// Inclui Rotas de Autenticação Auxiliares
require_once __DIR__ . '/auth.php';
