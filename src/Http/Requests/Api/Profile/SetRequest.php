<?php

namespace Alyani\Subsystem\Http\Requests\Api\Profile;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;

class SetRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'family' => ['nullable',  'string', 'max:255'],
            'nickname' => ['nullable',  'string', 'max:255'],
            'avatarSID' => ['nullable',  'string', 'max:255'],
        ];
    }
}
