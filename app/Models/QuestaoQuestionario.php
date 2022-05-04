<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class QuestaoQuestionario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questoes_questionarios';

    protected $fillable = [
        'questionario_id',
        'questao_id',
    ];

    public function rules()
    {
        return [
            'questionario_id' => [
                'required',
                'exists:questionarios,id',
                Rule::unique('questoes_questionarios')->where(function ($query) {
                    return $query->where('questionario_id', $this->questionario_id)
                        ->where('questao_id', $this->questao_id);
                })->ignore($this->id),
            ],
            'questao_id' => 'required:exists:questoes,id',
        ];
    }

    public function questionario()
    {
        return $this->belongsTo(Questionario::class, 'id', 'questionario_id');
    }

    public function questao()
    {
        return $this->hasOne(Questao::class, 'id', 'questao_id');
    }

    public function respostas()
    {
        return $this->hasMany(QuestaoQuestionarioResposta::class, 'questionario_questao_id', 'id');
    }
}
