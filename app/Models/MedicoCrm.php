<?php

namespace App\Models;

use App\Models\Enums\Estado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class MedicoCrm extends Model
{
    use HasFactory;

    protected $table = 'medicos_crm';

    protected $fillable = [
        'medico_id',
        'crm',
        'estado_sigla',
    ];

    public function rules()
    {
        return [
            'estado_sigla' => 'required|max:2',
            'medico_id' => [
                'required',
                'exists:users',
                Rule::unique('medicos_crm')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('crm', $this->crm);
                }),
            ],
            'crm' => [
                'required',
                Rule::unique('medicos_crm')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('crm', $this->crm);
                }),
            ],
        ];
    }

    public function medico()
    {
        return $this->hasOne(User::class, 'id', 'medico_id');
    }

    public function estado()
    {
        return Estado::estados($this->estado_sigla);
    }
}
