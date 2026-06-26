<?php

namespace Alyani\Subsystem\Http\Requests\Admin\Role;

use Illuminate\Validation\Rule;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;

class UpdateRequest extends WebRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->ignore($this->role->id)
                    ->where('guard_name', 'web'),
            ],

            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        $messages = parent::messages();
        return array_merge(
            $messages,
            [
                'permissions.required' => 'انتخاب حداقل یک مجوز الزامی‌ست',
                'permissions.min' => 'انتخاب حداقل یک مجوز الزامی‌ست',
            ]
        );
    }
}
