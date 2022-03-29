<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questionarios';

    protected $fillable = [
        'titulo',
        'descricao',
    ];
}
