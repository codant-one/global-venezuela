<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MigrantRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required'
            ],
            'last_name' => [
                'required'
            ],
            'email' => [
                'required',
                'email',
                'unique:migrants,email'
            ],
            'phone' => [
                'required'
            ],
            'gender_id' => [
                'required',
                'integer',
                'exists:App\Models\Gender,id'
            ],
            'birthdate' => [
                'required',
                'date_format:Y-m-d'
            ],
            'passport_number' => [
                'required',
                'unique:migrants,passport_number'
            ],
            'parish_id' => [
                'integer',
                'required',
                'exists:App\Models\Parish,id'
            ],
            'address' => [
                'required'
            ],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            //step 1
            'name.required' => 'El nombre es requerido.',
            'last_name.required' => 'El apellido es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El formato de correo electrónico no es permitido.',
            'email.unique' => 'Ya existe un usuario con el correo electrónico ingresado.',
            'phone.required' => 'El teléfono es requerido.',
            'gender_id.required' => 'El Genero es requerido',
            'gender_id.integer' => 'El formato del genero debe ser entero.',
            'gender_id.exists' => 'El Genero ingresado no existe.',
            'birthdate.required' => 'El nombre del Cliente es requerido.',
            'birthdate.date_format' => 'El formato de la fecha de cumpleaños es incorrecto (YYYY/mm/dd).',

            //step 2
            'passport_number.required' => 'El documento es requerido.',
            'passport_number.unique' => 'Ya existe un usuario con el documento ingresado.',

            //step 3
            'parish_id.required' => 'La parroquia es requerida.',
            'parish_id.integer' => 'El formato de la parroquia debe ser entero.',
            'parish_id.exists' => 'La parroquia ingresada no existe.',
            'address.required' => 'La dirección es requerida.',
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
