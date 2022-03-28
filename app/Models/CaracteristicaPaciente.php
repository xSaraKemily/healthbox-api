<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaracteristicaPaciente extends Model
{
    use HasFactory;

    protected $table = 'caracteristicas_paciente';

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

    public function rules()
    {
        return [
            'cpf'         => 'required|max:11',
            'peso'        => 'required|decimal',
            'altura'      => 'required|decimal',
            'sexo'        => 'required|in:feminino,masculino,outros',
            'paciente_id' => 'required|exists:users|unique:caracteristicas_paciente,paciente_id',
        ];
    }

    public function paciente()
    {
        $this->hasOne(User::class, 'id', 'paciente_id');
    }
}
