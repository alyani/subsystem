<?php

namespace Alyani\Subsystem\Http\Requests\Api\Storage;

use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Alyani\Subsystem\Http\Requests\Api\ApiRequest;

class UploadRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $storageRules = Config::get('subsystem.storage', []);
        return [
            'type' => ['required', Rule::in(array_keys($storageRules))],
            'file' => ['required', 'file'] + ($storageRules[$this->type]['validate'] ?? []),
        ];
    }
}
