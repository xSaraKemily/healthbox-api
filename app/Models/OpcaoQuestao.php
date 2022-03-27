<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcaoQuestao extends Model
{
    use HasFactory;

    protected $table = 'opcoes_questoes';

    protected $fillable = [
        'descricao',
        'questao_id',
    ];

    public function questao()
    {
        $this->hasOne(Questao::class, 'id', 'questao_id');
    }
}
