<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class CaracteristicaMedico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'caracteristicas_medico';

    protected $fillable = [
        'descricao',
        'medico_id',
    ];

    public function rules()
    {
        return [
            'descricao' => 'nullable|max:1000',
            'medico_id' => [
                'required',
                'exists:users,id',
                Rule::unique('caracteristicas_medico')->where('medico_id', $this->medico_id)->ignore($this->id)
            ]
        ];
    }

    public function medico()
    {
        $this->belongsTo(User::class, 'id', 'medico_id');
    }
}
