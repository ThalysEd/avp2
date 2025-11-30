<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;    // <--- Importe
use App\Http\Controllers\RankingController; // <--- Importe
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Página Inicial (Landing Page)
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// Rotas Protegidas (Dashboard e Quiz)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // DASHBOARD (Exibe o Ranking)
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard'); // Vamos editar esse arquivo depois
    })->name('dashboard');

    // API do Ranking (Para ser consumida pelo Vue dentro do Dashboard)
    Route::get('/api/ranking', [RankingController::class, 'index']);

    // QUIZ
    // Rota que carrega a TELA do Quiz
    Route::get('/quiz', function () {
        return Inertia::render('QuizRunner'); // Criaremos esse arquivo Vue
    })->name('quiz');

    // Rotas de DADOS do Quiz (Backend puro)
    Route::get('/api/quiz/start', [QuizController::class, 'start']);
    Route::post('/api/quiz/submit', [QuizController::class, 'submit']);

    // Perfil (Padrão Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';