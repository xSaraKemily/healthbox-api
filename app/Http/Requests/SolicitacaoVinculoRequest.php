<?php

namespace App\Http\Requests;

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
                        ->where('paciente_id', $this->paciente_id);
                }),
            ],
            'paciente_id'   => [
                'required',
                'exists:users',
                Rule::unique('solicitacoes_vinculos')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('paciente_id', $this->paciente_id);
                }),
            ],
            'vinculado'     => 'required|in:0,1'
        ];
    }
}
