<?php

namespace Alyani\Subsystem\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ApiRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function messages()
    {
        $rules = $this->rules();
        $messages = [];

        foreach ($rules as $field => $validations) {
            $validations = is_array($validations) ? $validations : explode('|', $validations);

            foreach ($validations as $validation) {
                $validationKey = explode(':', $validation)[0];
                $messages["$field.$validationKey"] = st(
                    $validationKey,
                    ['attribute' => $field],
                    'validation',
                );
            }
        }

        return $messages;
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        $errorData = [];
        foreach ($errors as $field => $messages) {
            $errorData[] = [
                'field' => $field,
                'message' => array_shift($messages),
            ];
        }

        $response = response()->json([
            'status' => 'error',
            'data' => [],
            'error' => $errorData,
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
