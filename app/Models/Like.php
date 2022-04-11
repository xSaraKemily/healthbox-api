<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Like extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'likes';

    protected $fillable = [
        'usuario_id',
        'opiniao_id',
        'is_like'
    ];

    public function rules()
    {
        return [
            'usuario_id' => [
                'required',
                'exists:users,id',
                Rule::unique('likes')->where(function ($query) {
                    return $query->where('usuario_id', $this->usuario_id)
                        ->where('opiniao_id', $this->opiniao_id)
                        ->whereNull('deleted_at');
                })->ignore($this->id),
            ],
            'opiniao_id' => [
                'required',
                'exists:opinioes,id',
                 Rule::unique('likes')->where(function ($query) {
                     return $query->where('usuario_id', $this->usuario_id)
                         ->where('opiniao_id', $this->opiniao_id)
                         ->whereNull('deleted_at');
                 })->ignore($this->id)
            ],
            'is_like'    => 'in:0,1'
        ];
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'id', 'usuario_id');
    }

    public function opiniao()
    {
        return $this->hasOne(Opiniao::class, 'id', 'opiniao_id');
    }
}
