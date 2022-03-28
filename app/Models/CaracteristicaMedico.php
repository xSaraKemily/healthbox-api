<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaracteristicaMedico extends Model
{
    use HasFactory;

    protected $table = 'caracteristicas_medico';

    protected $fillable = [
        'descricao',
        'medico_id',
    ];

    public function rules()
    {
        return [
            'descricao'        => 'nullable|max:500', //todo: analisar tamanho de caracteres
            'medico_id'        => 'required|exists:users|unique:caracteristicas_medicos,medico_id',
        ];
    }

    public function medico()
    {
        $this->hasOne(User::class, 'id', 'medico_id');
    }
}
