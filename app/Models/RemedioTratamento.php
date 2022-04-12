<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class RemedioTratamento extends Model
{
    use HasFactory;

    protected $table = 'remedios_tratamentos';

    protected $fillable = [
      'dose',
      'unidade_medida' ,
      'duracao',
      'intervalo',
      'periodicidade',
      'remedio_id',
      'tratamento_id',
    ];

    public function rules()
    {
        return [
            'dose'           => 'numeric',
            'unidade_medida' => 'in:MG,G,ML,GO,MGO',
            'duracao'        => 'numeric',
            'intervalo'      => 'numeric',
            'periodicidade'  => 'required|in:horas,dias',
            'remedio_id'     => [
                'required',
                'exists:remedios,id',
                Rule::unique('remedios_tratamentos')->where(function ($query) {
                    return $query->where('remedio_id', $this->remedio_id)
                        ->where('tratamento_id', $this->tratamento_id);
                })->ignore($this->id),
            ],
            'tratamento_id'  => [
                'required',
                'exists:tratamentos,id',
                    Rule::unique('remedios_tratamentos')->where(function ($query) {
                        return $query->where('remedio_id', $this->remedio_id)
                            ->where('tratamento_id', $this->tratamento_id);
                    })->ignore($this->id),
                ]
        ];
    }

    public function remedio()
    {
        return $this->hasOne(Remedio::class, 'id', 'remedio_id');
    }

    public function tratamento()
    {
        return $this->hasOne(Tratamento::class, 'id', 'tratamento_id');
    }
}
