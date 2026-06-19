<?php

namespace Alyani\Subsystem\Http\Requests\Api\Profile;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;

class SetPasswordRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => ['required', 'min:8', 'max:32'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'password' => strip_tags($this->password),
        ]);
    }
}
