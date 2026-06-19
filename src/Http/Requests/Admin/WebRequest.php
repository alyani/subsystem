<?php

namespace Alyani\Subsystem\Http\Requests\Admin;

use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Foundation\Http\FormRequest;

class WebRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function parseAmount($val): ?int
    {
        return replaceAmount($val);
    }

    public function parseTimeStamp($val): ?int
    {
        if (empty($val) || !preg_match('/^\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/', $val)) {
            return null;
        }
        return Verta::parse($val)->timestamp;
    }

    public function messages()
    {
        $rules = $this->rules();
        $messages = [];
        $attributes = $this->attributes();

        foreach ($rules as $field => $validations) {
            $validations = is_array($validations) ? $validations : explode('|', $validations);
            $label = $attributes[$field] ?? $field;
            foreach ($validations as $validation) {
                $validationKey = explode(':', $validation)[0];
                $messages["$field.$validationKey"] = st(
                    $validationKey,
                    ['attribute' => $label],
                    'validation',
                );
            }
        }

        return $messages;
    }
}
