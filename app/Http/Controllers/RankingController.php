<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        // Busca as 10 melhores tentativas
        $ranking = QuizAttempt::with('user:id,name') // Traz o nome do usuário junto (Eager Loading)
            ->orderBy('score', 'desc')     // 1º Critério: Maior Pontuação
            ->orderBy('total_time', 'asc') // 2º Critério: Menor Tempo (se empatar nos pontos)
            ->take(10)                     // Limita ao Top 10
            ->get();

        return response()->json($ranking);
    }
}