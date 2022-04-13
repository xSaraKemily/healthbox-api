<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AcompanhamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(auth()->user()->tipo == 'P') {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'descricao_paciente'       => 'nullable',
            'quantidade_periodicidade' => 'required|numeric',
            'dias_duracao'             => 'required|numeric',
            'data_inicio'              => 'nullable|date',
            'medico_id'                => 'required|exists:users,id',
            'paciente_id'              => 'required|exists:users,id',
            'questionario_id'          => 'nullable|exists:questionarios,id',
            'ativo'                    => 'in:1,0|default:1'
        ];
    }

    public function wantsJson()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()->all(),
        ], 422));
    }
}
