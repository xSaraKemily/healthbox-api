<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'opcao_id'                  => [
                'required_without:resposta_descritiva',
                'exists:opcoes_questoes,id',
                Rule::unique('tratamentos')->where(function ($query) {
                    return $query->where('opiniao_id', $this->request->get('opiniao_id'))
                        ->whereNull('deleted_at');
                })
            ],
            'questionario_questao_id'   => 'required|exists:questoes_questionarios,id',
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
