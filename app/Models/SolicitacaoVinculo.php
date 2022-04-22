<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitacaoVinculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitacoes_vinculos';

    protected $fillable = [
        'medico_id',
        'paciente_id',
        'vinculado'
    ];

    protected $attribute = [
        'vinculado' => 0
    ];

    public function paciente()
    {
        return $this->hasOne(User::class, 'id', 'paciente_id');
    }

    public function medico()
    {
        return $this->hasOne(User::class, 'id', 'medico_id');
    }
}
