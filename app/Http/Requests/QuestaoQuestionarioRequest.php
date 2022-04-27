<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class QuestaoQuestionarioRequest extends FormRequest
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
            'questionario_id' => [
                'required',
                'exists:questionarios,id',
                Rule::unique('questoes_questionarios')->where(function ($query) {
                    return $query->where('questionario_id', $this->request->get('questionario_id'))
                        ->where('questao_id', $this->request->get('questao_id'));
                })->ignore($this->id),
            ],
            'questao_id' => 'required:exists:questoes,id',
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
