<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questoes';

    protected $fillable = [
        'descricao',
        'tipo'
    ];
}
