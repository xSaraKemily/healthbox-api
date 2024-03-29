<?php

namespace App\Models;

use App\Http\Requests\MedicoCrmEspecializacaoRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class MedicoCrmEspecializacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medico_crm_especializacoes';

    protected $fillable = [
        'medico_crm_id',
        'especializacao_id',
    ];

    public function rules()
    {
        return [
            'medico_crm_id' => [
                'required',
                'exists:medicos_crm,id',
            ],
            'especializacao_id' => [
                'required',
                'exists:especializacoes,id',
                Rule::unique('medico_crm_especializacoes')->where(function ($query) {
                    return $query->where('medico_crm_id', $this->medico_crm_id)
                        ->where('especializacao_id', $this->especializacao_id);
                })->ignore($this->id),
            ]
        ];
    }

    public function crm()
    {
        return $this->hasOne(MedicoCrm::class, 'id', 'medico_crm_id');
    }

    public function especializacao()
    {
        return $this->hasOne(Especializacao::class, 'id', 'especializacao_id');
    }
}
