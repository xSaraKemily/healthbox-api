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
        'hash_pdf_bula',
        'api_id',
    ];

    public function rules()
    {
        return [
            'nome'              => 'required|max:255',
            'fabricante'        => 'required|max:255',
            'hash_pdf_bula'     => 'nullable|max:255',
            'api_id'            => 'required|max:255',
        ];
    }
}
