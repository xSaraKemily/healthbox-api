<?php

namespace App\Http\Requests;

use App\Models\CaracteristicaMedico;
use Illuminate\Foundation\Http\FormRequest;

class CaracteristicaMedicoRequest extends FormRequest
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
       return (new CaracteristicaMedico())->rules();
    }
}
