<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opiniao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'opinioes';

    protected $fillable = [
        'descricao',
        'paciente_id',
        'eficaz',
        'ativo'
    ];

    protected $attributes = ['ativo' => 1];

    public function rules()
    {
        return [
            'descricao'     => 'required',
            'paciente_id'   => 'required|exists:users,id',
            'eficaz'        => 'required|in:0,1',
            'ativo'         => 'required|in:0,1'
        ];
    }

    public function paciente()
    {
        return $this->hasOne(User::class, 'id', 'paciente_id');
    }

    public function tratamento()
    {
        return $this->hasOne(Tratamento::class, 'opiniao_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'opiniao_id', 'id');
    }
}
