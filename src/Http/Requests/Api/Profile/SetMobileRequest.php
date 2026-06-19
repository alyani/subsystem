<?php

namespace Alyani\Subsystem\Http\Requests\Api\Profile;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;

class SetMobileRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country_code' => ['required', 'integer', 'gt:0'],
            'mobile' => ['required', 'validMobile', 'unique:users'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->country_code = normalizeCountryCode($this->country_code) ?: 98;
        $this->mobile = normalizeMobile($this->mobile, $this->country_code) ?: $this->mobile;

        $this->merge([
            'country_code' => $this->country_code,
            'mobile' => $this->mobile,
        ]);
    }
}
