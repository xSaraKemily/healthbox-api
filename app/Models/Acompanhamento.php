<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acompanhamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'acompanhamentos';

    protected $fillable = [
        'descricao_paciente'       => 'nullable',
        'quantidade_periodicidade' => 'required|numeric',
        'dias_duracao'             => 'required|numeric',
        'data_inicio'              => 'nullable|date',
        'medico_id'                => 'required|exists:users',
        'paciente_id'              => 'required|exists:users',
        'questionario_id'          => 'required|exists:questionarios',
        'ativo'                    => 'required|in:0,1'
    ];

    protected $casts = [
        'data_inicio' => 'date',
    ];
}
