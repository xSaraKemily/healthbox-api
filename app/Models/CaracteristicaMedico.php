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
                Rule::exists('users', 'id')->where('tipo', 'M'),
                Rule::unique('caracteristicas_medico')->where('medico_id', $this->medico_id) ->whereNull('deleted_at')->ignore($this->id)
            ]
        ];
    }

    public function medico()
    {
        $this->belongsTo(User::class, 'id', 'medico_id');
    }
}
