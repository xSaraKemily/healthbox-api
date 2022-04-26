<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpcaoQuestao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'opcoes_questoes';

    protected $fillable = [
        'descricao',
        'questao_id',
    ];

    public function rules()
    {
        return [
            'descricao'  => 'required',
            'questao_id' => 'required|exists:questoes,id',
        ];
    }

    public function questao()
    {
        return $this->hasOne(Questao::class, 'id', 'questao_id');
    }
}
