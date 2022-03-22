<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaracteristicaPaciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'cpf',
        'peso',
        'altura',
        'sexo',
        'paciete_id',
    ];

    protected $casts = [
        'altura' => 'float',
        'peso'   => 'float'
    ];

    public function paciente()
    {
        $this->hasOne(User::class, 'id', 'paciente_id');
    }
}
