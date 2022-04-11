<?php

namespace App\Http\Requests;

use App\Models\Tratamento;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class TratamentoRequest extends FormRequest
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
    {;
        return [
            'opiniao_id'        => [
                'nullable',
                'required_without:acompanhamento_id',
                'exists:opinioes,id',
                Rule::unique('tratamentos')->where(function ($query) {
                    return $query->where('opiniao_id', $this->request->get('opiniao_id'))
                        ->whereNull('deleted_at');
                })
            ],
            'acompanhamento_id' => [
                'nullable',
                'required_without:opiniao_id',
                'exists:acompanhamentos,id',
                Rule::unique('tratamentos')->where(function ($query) {
                    return $query->where('acompanhamento_id', $this->request->get('acompanhamento_id'))
                        ->whereNull('deleted_at');
                })
            ],
            'descricao' => 'required'
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
