<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'tipo',
        'password',
        'data_nascimento',
        'telefone',
        'foto_path',
        'ativo',
    ];

    protected $attributes = [
        'ativo' => true
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'data_nascimento'   => 'date',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function rules()
    {
        return [
            'name'            => 'required|max:255',
            'tipo'            => 'required|in:M,P',
            'email'           => 'required|unique:users',
            'password'        => 'required',
            'data_nascimento' => 'nullable|date',
            'telefone'        => 'nullable|max:9|min:8',
            'foto_path'       => 'nullable',
            'ativo'           => 'required|in:0,1',
        ];
    }

    public function caracteristica()
    {
        //se for medico
        if($this->tipo == 'M') {
            return $this->hasOne(CaracteristicaMedico::class, 'medico_id', 'id');
        } else {
            //se for paciente
            return $this->hasOne(CaracteristicaPaciente::class, 'paciente_id', 'id');
        }
    }
}
