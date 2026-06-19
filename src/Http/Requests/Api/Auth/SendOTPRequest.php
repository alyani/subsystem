<?php

namespace Alyani\Subsystem\Http\Requests\Api\Auth;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class SendOTPRequest extends ApiRequest
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
                'mobile' => ['required', 'mobileCountryCode:country_code', 'validMobile'],
            ];
        } else {
            $authorityRules = [
                'email' => ['required', 'email'],
            ];
        }
        return array_merge($authorityRules, [
            'action' => Rule::in([
                'register',
                'resetPassword',
            ]),
            'password' => ['required_if:action,register', 'min:8', 'max:32'],
            'name' => ['exclude_unless:action,register', 'nullable', 'string', 'max:255'],
            'family' => ['exclude_unless:action,register', 'nullable', 'string', 'max:255'],
            'nickname' => ['exclude_unless:action,register', 'nullable', 'string', 'max:255'],
            'referral_code' => ['nullable', 'string', 'max:32'],
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

        $this->merge([
            'action' => $this->action ?: 'register',
        ]);
    }
}
