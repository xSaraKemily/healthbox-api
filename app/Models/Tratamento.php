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


    public function opiniao()
    {
        $this->hasOne(Opiniao::class, 'id', 'opiniao_id');
    }

    public function acompanhamento()
    {
        $this->hasOne(Tratamento::class, 'id', 'acompanhamento_id');
    }
}
