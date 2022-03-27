<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remedio extends Model
{
    use HasFactory;

    protected $table = 'remedios';

    protected $fillable = [
        'nome',
        'fabricante',
        'link_bula',
        'api_id',
    ];
}
