<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CRIAR USUÁRIO DE TESTE (ADMIN)
        // Login: admin@quiz.com / Senha: password
        $admin = User::create([
            'name' => 'Admin Developer',
            'email' => 'admin@quiz.com',
            'password' => Hash::make('password'),
        ]);

        // 2. CRIAR PERGUNTAS REAIS
        $questions = [
            [
                'statement' => 'Qual arquitetura padrão é utilizada pelo Laravel?',
                'options' => ['MVC (Model-View-Controller)', 'MVP (Model-View-Presenter)', 'MVVM (Model-View-ViewModel)', 'Flux'],
                'correct' => 0 // Índice da resposta correta (0 = primeira opção)
            ],
            [
                'statement' => 'No Vue.js 3, qual função é usada para criar estado reativo dentro do setup()?',
                'options' => ['data()', 'ref()', 'state()', 'make()'],
                'correct' => 1
            ],
            [
                'statement' => 'Qual comando do Artisan cria um novo controller?',
                'options' => ['php artisan new:controller', 'php artisan make:controller', 'php artisan create:controller', 'php artisan build:controller'],
                'correct' => 1
            ],
            [
                'statement' => 'Para qual finalidade serve o arquivo .env no Laravel?',
                'options' => ['Definir as rotas', 'Configurar variáveis de ambiente sensíveis', 'Compilar o CSS', 'Gerenciar dependências do Composer'],
                'correct' => 1
            ],
            [
                'statement' => 'Qual diretiva do Vue é usada para renderização condicional?',
                'options' => ['v-for', 'v-bind', 'v-if', 'v-on'],
                'correct' => 2
            ],
            // Você pode adicionar mais perguntas aqui seguindo o padrão
        ];

        foreach ($questions as $q) {
            $question = Question::create(['statement' => $q['statement']]);

            foreach ($q['options'] as $index => $text) {
                Option::create([
                    'question_id' => $question->id,
                    'text' => $text,
                    'is_correct' => $index === $q['correct']
                ]);
            }
        }

        // 3. POPULAR RANKING (Cria usuários fictícios e pontuações)
        // Cria 10 usuários aleatórios
        User::factory(10)->create()->each(function ($user) {
            // Para cada usuário, cria uma tentativa de quiz aleatória
            QuizAttempt::create([
                'user_id' => $user->id,
                'score' => rand(0, 5) * 10, // Pontuação entre 0 e 50
                'total_time' => rand(30, 120), // Tempo entre 30s e 2min
                'correct_answers' => rand(0, 5),
                'wrong_answers' => rand(0, 5)
            ]);
        });
    }
}