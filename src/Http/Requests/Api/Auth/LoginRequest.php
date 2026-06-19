<?php

namespace Alyani\Subsystem\Http\Requests\Api\Auth;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;

class LoginRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $authorityKey = config('subsystem.signupAuthorityKey');
        $authorityRules = [];
        if ($authorityKey === 'mobile') {
            $authorityRules = [
                'country_code' => ['required', 'integer', 'gt:0'],
                'mobile' => ['required', 'mobileCountryCode:country_code'],
            ];
        } else {
            $authorityRules = [
                'email' => ['required', 'email'],
            ];
        }

        return array_merge($authorityRules, [
            'password' => ['required', 'string', 'max:32'],
        ]);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (config('subsystem.signupAuthorityKey') === 'mobile') {
            $this->country_code = normalizeCountryCode($this->country_code) ?: 98;
            $this->mobile = normalizeMobile($this->mobile, $this->country_code) ?: $this->mobile;
            $this->merge([
                'country_code' => $this->country_code,
                'mobile' => $this->mobile,
            ]);
        } else {
            $this->merge([
                'email' => strip_tags(strtolower(replacePersianDigistWithEnglish($this->email))),
            ]);
        }
    }
}
