<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SolicitacaoVinculoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'medico_id'     => [
                'required',
                Rule::exists('users', 'id')->where('tipo', 'M'),
                Rule::unique('solicitacoes_vinculos')->where(function ($query) {
                    return $query->where('medico_id', $this->request->get('medico_id'))
                        ->where('paciente_id', $this->request->get('paciente_id'))
                        ->whereNull('deleted_at');
                }),
            ],
            'paciente_id'   => [
                'required',
                Rule::exists('users', 'id')->where('tipo', 'P'),
                Rule::unique('solicitacoes_vinculos')->where(function ($query) {
                    return $query->where('medico_id', $this->request->get('medico_id'))
                        ->where('paciente_id', $this->request->get('paciente_id'))
                        ->whereNull('deleted_at');
                }),
            ],
            'vinculado'     => 'in:0,1'
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
