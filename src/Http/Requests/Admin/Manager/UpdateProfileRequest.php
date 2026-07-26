<?php

namespace Alyani\Subsystem\Http\Requests\Admin\Manager;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;

class UpdateProfileRequest extends WebRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $manager = auth()->user();
        return [
            'name' => ['required', 'string', 'min:3', 'max:191'],
            'family' => ['required', 'string', 'min:3', 'max:191'],
            'mobile' => [
                'required',
                'string',
                'validMobile',
                Rule::unique('managers', 'mobile')->ignore($manager->id),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                Rule::unique('managers', 'email')->ignore($manager->id)
            ],
            'avatar' => ['nullable', 'image'] + config('subsystem.storage.image.validate'), // حداکثر 2MB
            'currenct_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string'],
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
