<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Tratamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tratamentos';

    protected $fillable = [
        'opiniao_id',
        'acompanhamento_id',
        'titulo',
        'descricao'
    ];

    public function rules()
    {
        return [
            'opiniao_id'  => [
                    'nullable',
                    'required_without:acompanhamento_id',
                    'exists:opinioes,id',
                    Rule::unique('tratamentos')->where('opiniao_id', $this->opiniao_id)
                        ->whereNull('deleted_at')
                        ->ignore($this->id),
                ],
            'acompanhamento_id' => [
                'nullable',
                'required_without:opiniao_id',
                'exists:acompanhamentos,id',
                 Rule::unique('tratamentos')
                     ->where('acompanhamento_id', $this->acompanhamento_id)
                     ->whereNull('deleted_at')
                     ->ignore($this->id)
            ],
            'descricao' => 'nullable',
            'titulo'    => 'required|max:50'
        ];
    }

    public function opiniao()
    {
        return $this->hasOne(Opiniao::class, 'id', 'opiniao_id');
    }

    public function tratamento()
    {
        return $this->hasOne(Tratamento::class, 'id', 'tratamento_id');
    }

    public function acompanhamento()
    {
        return $this->hasOne(Acompanhamento::class, 'id', 'acompanhamento_id');
    }

    public function remedios()
    {
        return $this->hasMany(RemedioTratamento::class, 'tratamento_id', 'id');
    }
}
