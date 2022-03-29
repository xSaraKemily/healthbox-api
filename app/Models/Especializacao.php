<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Especializacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'especializacoes';

    protected $fillable = [
        'nome',
    ];

    public function medico()
    {
        $this->belongsToMany('id', 'users', 'id');
    }
}
