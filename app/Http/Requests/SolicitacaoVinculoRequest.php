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
            'medico_id'     => [
                'required',
                'exists:users',
                Rule::unique('solicitacoes_vinculos')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('paciente_id', $this->paciente_id)
                        ->whereNull('deleted_at');
                }),
            ],
            'paciente_id'   => [
                'required',
                'exists:users',
                Rule::unique('solicitacoes_vinculos')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('paciente_id', $this->paciente_id)
                        ->whereNull('deleted_at');
                }),
            ],
            'vinculado'     => 'required|in:0,1'
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
