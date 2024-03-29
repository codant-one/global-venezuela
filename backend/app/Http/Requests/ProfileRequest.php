<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'parish_id' => [
                'integer',
                'required',
                'exists:App\Models\Parish,id'
            ],
            'phone' => [
                'required'
            ],
            'address' => [
                'required'
            ],
            'last_name' => [
                'required'
            ],
            'image' => [
                'required'
            ]
         
        ];
    }

    public function messages()
    {
        return [
            'parish_id.required' => 'La parroquia es requerida.',
            'parish_id.integer' => 'El formato de parroquia debe ser entero.',
            'parish_id.exists' => 'La parroquia ingresada no existe.',
            'phone.required' => 'El teléfono es requerido.',
            'address.required' => 'La dirección es requerida.',
            'last_name.required' => 'El apellido es requerido.',
            'image.required' => 'La imagen es requerida.',
        ];
    }

    /**
    * Get the error messages for the defined validation rules.*
    * @return array
    */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'feedback' => 'params_validation_failed',
            'message' => $validator->errors()
        ], 400));
    }
}
