<?php

namespace App\Models;

use App\Models\Enums\Estado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class MedicoCrm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medicos_crm';

    protected $fillable = [
        'medico_id',
        'crm',
        'estado_sigla',
    ];

    public function rules()
    {
        return [
            'estado_sigla' => 'required|max:2',
            'medico_id' => [
                'required',
                'exists:users,id',
                Rule::unique('medicos_crm')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('crm', $this->crm);
                })->ignore($this->id),
            ],
            'crm' => [
                'required',
                Rule::unique('medicos_crm')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('crm', $this->crm);
                })->ignore($this->id),
            ],
        ];
    }

    public function medico()
    {
        return $this->hasOne(User::class, 'id', 'medico_id');
    }

    public function estado()
    {
        return Estado::estados($this->estado_sigla);
    }

    public function especializacoes()
    {
        return $this->hasMany(MedicoCrmEspecializacao::class, 'medico_crm_id', 'id');
    }
}
