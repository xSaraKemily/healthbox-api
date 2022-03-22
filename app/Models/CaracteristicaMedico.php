<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaracteristicaMedico extends Model
{
    use HasFactory;

    protected $table = 'caracteristicas_medico';

    protected $fillable = [
        'crm',
        'estado_sigla',
        'descricao',
        'medico_id',
        'especialiacao_id',
    ];

    public function medico()
    {
        $this->hasOne(User::class, 'id', 'medico_id');
    }

    public function especializacao()
    {
        $this->hasOne(Especializacao::class, 'id', 'especialiacao_id');
    }
}
