<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RemedioTratamentoRequest extends FormRequest
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
            'dose'           => 'decimal',
            'unidade_medida' => 'max:255',
            'duracao'        => 'numeric',
            'intervalo'      => 'numeric',
            'periodicidade'  => 'required|in:horas,dias',
            'remedio_id'     => 'required|exists:remedios',
            'tratamento_id'  => 'required|exists:tratamentos',
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
