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
            'descricao' => 'nullable|max:1000',
            'medico_id' => 'required|exists:users,id|unique:caracteristicas_medico',
        ];
    }

    public function medico()
    {
        $this->belongsTo(User::class, 'id', 'medico_id');
    }
}
