<?php

namespace Alyani\Subsystem\Http\Requests\Admin\Manager;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;

class CreateManagerRequest extends WebRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:191'],
            'family' => ['required', 'string', 'min:3', 'max:191'],
            'mobile' => ['required', 'string', 'validMobile', Rule::unique('managers', 'mobile')],
            'email' => ['nullable', 'string', 'email', Rule::unique('managers', 'email')],
            'avatar' => ['nullable', 'image'] + config('subsystem.storage.image.validate'), // حداکثر 2MB
            'password' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mobile' => normalizeMobile(ltrim($this->mobile, 0)),
            'name' => trim($this->name),
            'family' => trim($this->family),
        ]);
    }
}
