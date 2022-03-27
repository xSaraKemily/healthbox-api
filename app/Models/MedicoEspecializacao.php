<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicoEspecializacao extends Model
{
    use HasFactory;

    protected $table = 'medico_especializacoes';

    protected $fillable = [
        'medico_id',
        'especializacao_id',
    ];

    public function medico()
    {
        return $this->hasOne(User::class, 'id', 'medico_id');
    }

    public function especializacao()
    {
        return $this->hasOne(Especializacao::class, 'id', 'especializacao_id');
    }
}
