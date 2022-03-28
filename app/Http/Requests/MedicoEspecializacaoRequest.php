<?php

namespace App\Http\Requests;

use App\Models\MedicoEspecializacao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedicoEspecializacaoRequest extends FormRequest
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
        return (new MedicoEspecializacao())->rules();
    }
}
