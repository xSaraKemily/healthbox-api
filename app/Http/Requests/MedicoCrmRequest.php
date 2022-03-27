<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            'medico_id' => [
                'required',
                'exists:users',
                Rule::unique('medicos_crm')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('crm', $this->crm);
                }),
            ],
            'crm' => [
                'required',
                Rule::unique('medicos_crm')->where(function ($query) {
                    return $query->where('medico_id', $this->medico_id)
                        ->where('crm', $this->crm);
                }),
            ],
        ];
    }
}
