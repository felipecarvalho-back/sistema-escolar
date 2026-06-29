<?php

use Core\Routing\Route;
use App\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);



Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [\App\Controllers\DashboardController::class, 'index'])->name('dashboard')->middleware(\App\Middleware\AuthMiddleware::class);
