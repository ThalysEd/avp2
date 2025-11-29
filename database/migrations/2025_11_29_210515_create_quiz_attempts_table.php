<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            
            // Relaciona a tentativa ao usuário
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->integer('score'); // Pontuação final
            $table->integer('total_time'); // Tempo total em segundos
            
            // Campos para estatísticas detalhadas
            $table->integer('correct_answers'); // Qtd de acertos
            $table->integer('wrong_answers');   // Qtd de erros
            
            $table->timestamps(); // O created_at servirá como a data da realização do quiz

            // INDEXAÇÃO:
            // Como teremos um Ranking na Home, é vital indexar a pontuação e o tempo
            // para que a consulta do banco seja rápida quando tiver muitos registros.
            $table->index(['score', 'total_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};