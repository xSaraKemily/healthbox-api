<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
}
