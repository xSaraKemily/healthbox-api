<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remedio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'remedios';

    protected $fillable = [
        'nome',
        'fabricante',
        'link_bula',
        'api_id',
    ];
}
