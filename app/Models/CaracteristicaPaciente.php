<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class CaracteristicaPaciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'caracteristicas_paciente';

    protected $fillable = [
        'cpf',
        'peso',
        'altura',
        'paciete_id',
        'comorbidades',
        'pre_disposicoes',
        'alergias_remedios',
    ];

    protected $casts = [
        'altura' => 'float',
        'peso'   => 'float'
    ];

    public function rules()
    {
        return [
            'cpf'         => 'required|max:30',
            'peso'        => 'required|numeric',
            'altura'      => 'required|numeric',
            'paciente_id' => [
                'required',
                Rule::exists('users', 'id')->where('tipo', 'P'),
                Rule::unique('caracteristicas_paciente') ->whereNull('deleted_at')->where('paciente_id', $this->paciente_id)->ignore($this->id)
            ],
            'comorbidades'      => 'nullable',
            'pre_disposicoes'   => 'nullable',
            'alergias_remedios' => 'nullable',
        ];
    }

    public function paciente()
    {
        $this->hasOne(User::class, 'id', 'paciente_id');
    }
}
