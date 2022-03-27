<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcompanhamentoRequest extends FormRequest
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
            'descricao_paciente' => 'nullable',
            'quantidade_periodicidade',
            'dias_duracao',
            'data_inicio',
            'medico_id',
            'paciente_id',
            'questionario_id',
            'ativo'
        ];
    }
}
