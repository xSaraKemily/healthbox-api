<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tratamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opiniao_id',
        'acompanhamento_id',
        'descricao'
    ];

    public function rules()
    {
        return [
            'opiniao_id'        => 'required_without:acompanhamento_id|exists:opinioes',
            'acompanhamento_id' => 'required_without:opiniao_id|exists:acompanhamentos',
            'descricao'         => 'required'
        ];
    }

    public function opiniao()
    {
        $this->hasOne(Opiniao::class, 'id', 'opiniao_id');
    }

    public function acompanhamento()
    {
        $this->hasOne(Tratamento::class, 'id', 'acompanhamento_id');
    }
}
