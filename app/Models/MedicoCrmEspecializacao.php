<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class MedicoCrmEspecializacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medico_crm_especializacoes';

    protected $fillable = [
        'medico_crm_id',
        'especializacao_id',
    ];

    public function crm()
    {
        return $this->hasOne(MedicoCrm::class, 'id', 'medico_crm_id');
    }

    public function especializacao()
    {
        return $this->hasOne(Especializacao::class, 'id', 'especializacao_id');
    }
}
