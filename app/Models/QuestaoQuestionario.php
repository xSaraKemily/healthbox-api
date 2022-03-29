<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestaoQuestionario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questoes_questionarios';

    protected $fillable = [
        'questionario_id',
        'questao_id',
    ];

    public function questionario()
    {
        $this->hasOne(Questionario::class, 'id', 'questionario_id');
    }

    public function questao()
    {
        $this->hasOne(Questao::class, 'id', 'questao_id');
    }
}
