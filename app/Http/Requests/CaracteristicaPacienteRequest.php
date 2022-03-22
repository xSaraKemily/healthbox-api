<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CaracteristicaPacienteRequest extends FormRequest
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
            'cpf'         => 'required|max:11',
            'peso'        => 'required|decimal',
            'altura'      => 'required|decimal',
            'sexo'        => 'required|in:feminino,masculino,outros',
            'paciente_id' => 'required|exists:users|unique:caracteristicas_paciente,paciente_id',
        ];
    }
}
