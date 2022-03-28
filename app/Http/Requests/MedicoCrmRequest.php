<?php

namespace App\Http\Requests;

use App\Models\MedicoCrm;
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
        return (new MedicoCrm())->rules();
    }
}
