<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $fillable = [
        'usuario_id',
        'opiniao_id',
        'is_like'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class, 'id', 'usuario_id');
    }

    public function opiniao()
    {
        return $this->hasOne(Opiniao::class, 'id', 'opiniao_id');
    }
}
