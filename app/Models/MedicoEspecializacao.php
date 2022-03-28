<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class MedicoEspecializacao extends Model
{
    use HasFactory;

    protected $table = 'medicos_especializacoes';

    protected $fillable = [
        'medico_id',
        'especializacao_id',
    ];

    public function rules()
    {
        return [
            'medico_id' => [
                'required',
                'exists:users',
                Rule::unique('medicos_especializacoes')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('especializacao_id', $this->especializacao_id);
                }),
            ],
            'especializacao_id' => [
                'required',
                'exists:especializacoes',
                Rule::unique('medicos_especializacoes')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('especializacao_id', $this->especializacao_id);
                }),
            ]
        ];
    }

    public function medico()
    {
        return $this->hasOne(User::class, 'id', 'medico_id');
    }

    public function especializacao()
    {
        return $this->hasOne(Especializacao::class, 'id', 'especializacao_id');
    }
}
