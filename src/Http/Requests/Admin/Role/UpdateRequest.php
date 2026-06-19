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
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($this->role->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
