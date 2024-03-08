<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Support\Facades\Log;

class UserRequest extends FormRequest
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
        // Log::info($this->user);

        return [
            'name' => [
                'required'
            ],
            'last_name' => [
                'required'
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user)
            ],
            'password' => [
                ($this->user) ? 'nullable' : 'required'
            ],
            'roles' => [
                'required',
                'array',
                'exists:Spatie\Permission\Models\Role,name'
            ],
            'parish_id' => [
                'integer',
                'required',
                'exists:App\Models\Parish,id'
            ]    
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido.',
            'last_name.required' => 'El apellido es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El formato de correo electrónico no es permitido.',
            'email.unique' => 'Ya existe un usuario con el correo electrónico ingresado.',
            'password.required' => 'La contraseña es requerida.',
            'roles.required' => 'El rol es requerido.',
            'roles.array' => 'El formato de roles no es permitido.',
            'roles.exists' => 'El rol ingresado no existe.',
            'parish_id.required' => 'La parroquia es requerida.',
            'parish_id.integer' => 'El formato de parroquia debe ser entero.',
            'parish_id.exists' => 'La parroquia ingresada no existe.'
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
