<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            // Chave estrangeira ligada à pergunta. 
            // onDelete('cascade') garante que se apagar a pergunta, as opções somem também.
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            
            $table->string('text'); // O texto da opção
            $table->boolean('is_correct')->default(false); // Verdadeiro apenas para a resposta certa
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};