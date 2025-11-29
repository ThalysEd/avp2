<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Inicia o Quiz
     * Retorna 10 perguntas aleatórias, mas SEM dizer qual é a correta.
     */
    public function start()
    {
        // 1. Pegar 10 perguntas aleatórias
        // 2. Carregar as opções, mas selecionando APENAS id, question_id e text
        //    (Isso evita enviar o campo 'is_correct' para o navegador)
        $questions = Question::with(['options' => function($query) {
            $query->select('id', 'question_id', 'text'); 
        }])
        ->inRandomOrder()
        ->take(10)
        ->get();

        return response()->json($questions);
    }

    /**
     * Recebe as respostas, corrige e salva o histórico.
     */
    public function submit(Request $request)
    {
        // Validação básica
        $request->validate([
            'answers' => 'required|array', // Array de objetos {question_id, option_id}
            'answers.*.question_id' => 'required|integer',
            'answers.*.option_id' => 'required|integer',
            'time' => 'required|integer' // Tempo em segundos
        ]);

        $answers = $request->input('answers');
        $totalTime = $request->input('time');
        
        $score = 0;
        $correctCount = 0;
        $wrongCount = 0;

        // Loop para corrigir cada resposta
        foreach ($answers as $userAnswer) {
            // Busca a opção no banco para ver se é a correta
            // Usamos find() para garantir que a opção existe
            $option = Option::find($userAnswer['option_id']);

            // Verificação de segurança:
            // 1. A opção existe?
            // 2. A opção pertence mesmo àquela pergunta?
            // 3. Ela é a correta?
            if ($option && $option->question_id == $userAnswer['question_id'] && $option->is_correct) {
                $score += 10; // 10 pontos por acerto (pode ajustar como quiser)
                $correctCount++;
            } else {
                $wrongCount++;
            }
        }

        // Salvar no Banco de Dados
        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(), // Pega o ID do usuário logado automaticamente
            'score' => $score,
            'total_time' => $totalTime,
            'correct_answers' => $correctCount,
            'wrong_answers' => $wrongCount,
        ]);

        return response()->json([
            'message' => 'Quiz finalizado com sucesso!',
            'score' => $score,
            'stats' => $attempt
        ], 201);
    }
}