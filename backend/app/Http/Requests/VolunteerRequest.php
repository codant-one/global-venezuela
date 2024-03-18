<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VolunteerRequest extends FormRequest
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
            'responsible.document' => [
                function($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    if (\App\Models\Volunteer::where('document', $value)->whereNotNull('document')->exists()) {
                        $fail('El documento ' . $value . ' ya está registrado.');
                    }
                }
            ],
            'responsible.email' => [
                function($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    if (\App\Models\Volunteer::where('email', $value)->whereNotNull('email')->exists()) {
                        $fail('El e-mail ' . $value . ' ya está registrado.');
                    }
                }
            ],
            'form.*.document' => [
                function($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    if (\App\Models\Volunteer::where('document', $value)->whereNotNull('document')->exists()) {
                        $fail('El documento ' . $value . ' ya está registrado.');
                    }
                }
            ],
            'form.*.email' => [
                function($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    if (\App\Models\Volunteer::where('email', $value)->whereNotNull('email')->exists()) {
                        $fail('El e-mail ' . $value . ' ya está registrado.');
                    }
                }
            ],
            'theme_id' => [
                'integer',
                'required',
                'exists:App\Models\Theme,id'
            ]
        ];

        if (request('state_id') && request('state_id') !== null) {
            $rules['state_id'] = 'integer|exists:App\Models\State,id';
        }

        if (request('municipality_id') && request('municipality_id') !== null) {
            $rules['municipality_id'] = 'integer|exists:App\Models\Municipality,id';
        }

        if (request('circuit_id') && request('circuit_id') !== null) {
            $rules['circuit_id'] = 'integer|exists:App\Models\Circuit,id';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'theme_id.required' => 'El tema es requerida.',
            'theme_id.integer' => 'El formato del tema debe ser entero.',
            'theme_id.exists' => 'El tema ingresado no existe.',
            'state_id.integer' => 'El formato de estado debe ser entero.',
            'state_id.exists' => 'El estado ingresado no existe.',
            'municipality_id.integer' => 'El formato del municipio debe ser entero.',
            'municipality_id.exists' => 'El municipio ingresado no existe.',
            'circuit_id.integer' => 'El formato del circuito debe ser entero.',
            'circuit_id.exists' => 'El circuito ingresado no existe.',
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
