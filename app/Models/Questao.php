<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questoes';

    protected $fillable = [
        'descricao',
        'tipo',
        'usuario_id'
    ];

    public function rules()
    {
        return [
            'descricao' => 'required',
            'tipo'      => 'required|in:O,D'
        ];
    }

    public function opcoes()
    {
        return $this->hasMany(OpcaoQuestao::class, 'questao_id');
    }
}
