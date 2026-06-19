<?php

namespace Alyani\Subsystem\Http\Requests\Api\Profile;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ChangePasswordRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'oldPassword' => ['required', 'string', 'max:32'],
            'newPassword' => ['required', 'string', 'min:8', 'max:32', 'confirmed:newPasswordConfirmation', 'different:oldPassword'],
            'newPasswordConfirmation' => ['required', 'string'],
        ];
    }
}
