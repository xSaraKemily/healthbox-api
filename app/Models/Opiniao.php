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
        'tratamento_id',
        'eficaz',
        'ativo'
    ];

    public function paciente()
    {
        $this->hasOne(User::class, 'id', 'paciente_id');
    }

    public function tratamento()
    {
        $this->hasOne(Tratamento::class, 'id', 'tratamento_id');
    }
}
