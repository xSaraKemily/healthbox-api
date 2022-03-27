<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Questionario extends Model
{
    use HasFactory;

    protected $table = 'questionarios';

    protected $fillable = [
        'titulo',
        'descricao',
    ];
}
