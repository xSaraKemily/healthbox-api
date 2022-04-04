<?php

namespace App\Http\Requests;

use App\Models\MedicoCrm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class MedicoCrmRequest extends FormRequest
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
            'estado_sigla' => 'required|max:2',
            'medico_id' => [
                'required',
                Rule::exists('users', 'id')->where('tipo', 'M'),
                Rule::unique('medicos_crm')->where(function ($query) {
                    return $query->where('medico_id', $this->request->get('medico_id'))
                        ->where('estado_sigla', $this->request->get('estado_sigla'))
                        ->whereNull('deleted_at');
                }),
            ],
            'crm' => [
                'required',
                Rule::unique('medicos_crm')->where(function ($query) {
                    return $query->where('crm', $this->request->get('crm'))
                        ->whereNull('deleted_at');
                }),
            ],
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
