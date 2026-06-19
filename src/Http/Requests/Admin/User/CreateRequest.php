<?php

namespace Alyani\Subsystem\Http\Requests\Admin\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;

class CreateRequest extends WebRequest
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
            'family' => ['nullable', 'string', 'min:3', 'max:191'],
            'nickname' => ['nullable', 'string', 'min:3', 'max:191'],
            'country_code' => ['required', 'integer', 'gt:0'],
            'mobile' => ['required', 'string', 'validMobile', Rule::unique('users', 'mobile')],
            'email' => ['nullable', 'string', 'email'],
            'password' => ['required', 'string', 'min:4', 'max:191'],
            'phone' => ['nullable', 'string', 'max:191'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Prepare country_code
        $country_code = normalizeCountryCode($this->country_code) ?: $this->country_code;
        // Prepare mobile
        $mobile = normalizeMobile($this->mobile, $country_code) ?: $this->mobile;

        $this->merge([
            'mobile' => $mobile,
            'country_code' => $country_code,
            'name' => trim($this->name),
            'family' => trim($this->family),
        ]);
    }
}
