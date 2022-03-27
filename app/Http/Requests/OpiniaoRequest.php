<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpiniaoRequest extends FormRequest
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
            'descricao'     => 'required',
            'paciente_id'   => 'required|exists:users',
            'tratamento_id' => 'nullable|exists:tratamentos',
            'eficaz'        => 'required|in:0,1',
            'ativo'         => 'required|in:0,1'
        ];
    }
}
