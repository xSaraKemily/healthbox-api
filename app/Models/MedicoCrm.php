<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicoCrm extends Model
{
    use HasFactory;

    protected $table = 'medicos_crm';

    protected $fillable = [
        'medico_id',
        'crm',
    ];

    public function medico()
    {
        return $this->hasOne(User::class, 'id', 'medico_id');
    }
}
