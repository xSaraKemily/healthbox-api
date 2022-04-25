<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questionarios';

    protected $fillable = [
        'titulo',
        'descricao',
        'acompanhamento_id'
    ];

    public function rules()
    {
        return [
            'titulo'            => 'required|max:255',
            'descricao'         => 'nullable',
            'acompanhamento_id' => 'required|exists:acompanhamentos,id'
        ];
    }

    public function questoes()
    {
        return $this->hasMany(QuestaoQuestionario::class, 'questionario_id', 'id');
    }

    public function acompanhamento()
    {
        return $this->belongsTo(Acompanhamento::class, 'acompanhamento_id');
    }
}
