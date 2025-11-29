<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'text', 'is_correct'];

    // Converte automaticamente o tinyint do banco para boolean no PHP
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // Relacionamento: Uma opção pertence a uma pergunta
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}