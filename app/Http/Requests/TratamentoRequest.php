<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TratamentoRequest extends FormRequest
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
            'opiniao_id'        => 'required_without:acompanhamento_id|exists:opinioes',
            'acompanhamento_id' => 'required_without:opiniao_id|exists:acompanhamentos',
            'descricao'         => 'required'
        ];
    }
}
