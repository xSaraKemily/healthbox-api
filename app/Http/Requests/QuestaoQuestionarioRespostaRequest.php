<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestaoQuestionarioRespostaRequest extends FormRequest
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
            'resposta_descritiva'       => 'required_without:opcao_id',
            'opcao_id'                  => 'required_without:resposta_descritiva|exists:opcoes_questoes',
            'questionario_questao_id'   => 'required|exists:questoes_questionarios',
        ];
    }
}
