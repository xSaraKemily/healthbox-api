<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestaoQuestionarioRequest extends FormRequest
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
            'questionario_id' => 'required:exists:questionarios',
            'questao_id'      => 'required:exists:questoes',
        ];
    }
}
