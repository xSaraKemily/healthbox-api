<?php

namespace App\Http\Requests;

use App\Models\MedicoCrmEspecializacao;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class MedicoCrmEspecializacaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(auth()->user()->tipo == 'M') {
            return true;
        }

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
            'medico_crm_id' => [
                'required',
                'exists:medicos_crm,id',
            ],
            'especializacao_id' => [
                'required',
                'exists:especializacoes,id',
                Rule::unique('medico_crm_especializacoes')->where(function ($query) {
                    return $query->where('medico_crm_id', $this->request->get('medico_crm_id'))
                        ->where('especializacao_id', $this->request->get('especializacao_id'));
                })->ignore($this->request->get('id')),
            ]
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
