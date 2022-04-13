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
        return $this->hasOne(Questionario::class, 'id', 'questionario_id');
    }

    public function questao()
    {
        return $this->hasOne(Questao::class, 'id', 'questao_id');
    }

    public function resposta()
    {
        return $this->hasOne(QuestaoQuestionarioResposta::class, 'questionario_questao_id', 'id');
    }
}
