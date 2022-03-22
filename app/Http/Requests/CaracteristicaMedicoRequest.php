<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CaracteristicaMedicoRequest extends FormRequest
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
            'crm'              => 'required|unique:caracteristicas_medicos,crm',
            'estado_sigla'     => 'required',
            'descricao'        => 'nullable|max:500', //todo: analisar tamanho de caracteres
            'medico_id'        => 'required|exists:users|unique:caracteristicas_medicos,medico_id',
            'especialiacao_id' => 'nullable|exists:especializacoes',
        ];
    }
}
