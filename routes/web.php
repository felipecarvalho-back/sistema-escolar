<?php

use App\Controllers\HomeController;
use App\Controllers\OcorrenciaController;
use App\Controllers\DashboardController;
use Core\Routing\Route;

// ==========================================
// ROTAS DE APLICAÇÃO (WEB / HTML)
// ==========================================

Route::post('/ocorrencias', [OcorrenciaController::class, 'store'])->middleware('auth');
Route::post('/ocorrencias/{id}/aprovar', [OcorrenciaController::class, 'approve'])->middleware('auth');
Route::post('/ocorrencias/{id}/rejeitar', [OcorrenciaController::class, 'reject'])->middleware('auth');

Route::post('/secretaria/alunos', [DashboardController::class, 'storeAluno'])->middleware('auth');
Route::post('/secretaria/turmas', [DashboardController::class, 'storeTurma'])->middleware('auth');
Route::post('/secretaria/vincular-turma', [DashboardController::class, 'vincularTurma'])->middleware('auth');
Route::post('/secretaria/vincular-responsavel', [DashboardController::class, 'vincularResponsavel'])->middleware('auth');
Route::post('/secretaria/usuarios', [DashboardController::class, 'storeUsuario'])->middleware('auth');

// Inclui Rotas de Autenticação Auxiliares
require_once __DIR__ . '/auth.php';
