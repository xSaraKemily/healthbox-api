<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitacaoVinculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitacoes_vinculos';

    protected $fillable = [
        'medico_id',
        'paciente_id',
        'vinculado',
        'solicitante_id'
    ];

    protected $attributes = [
        'vinculado' => 0
    ];

    protected $appends = [
        'solicitado'
    ];

    public function getSolicitadoAttribute()
    {
        if ($this->solicitante_id == $this->medico_id) {
            return User::find($this->paciente_id);
        }

        return User::find($this->medico_id);
    }

    public function paciente()
    {
        return $this->hasOne(User::class, 'id', 'paciente_id');
    }

    public function medico()
    {
        return $this->hasOne(User::class, 'id', 'medico_id');
    }

    public function solicitante()
    {
        return $this->hasOne(User::class, 'id', 'solicitante_id');
    }
}
