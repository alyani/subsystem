<?php

namespace Alyani\Subsystem\Http\Requests\Admin\Manager;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Alyani\Subsystem\Enums\ManagerStatus;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;

class UpdateManagerRequest extends WebRequest
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
            'mobile' => [
                'required',
                'string',
                'validMobile',
                Rule::unique('managers', 'mobile')->ignore($this->manager->id),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                Rule::unique('managers', 'email')->ignore($this->manager->id)
            ],
            'avatar' => ['nullable', 'image'] + config('subsystem.storage.image.validate'), // حداکثر 2MB
            'password' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:' . implode(',', ManagerStatus::values())],
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
