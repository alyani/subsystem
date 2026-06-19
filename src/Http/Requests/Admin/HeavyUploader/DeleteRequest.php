<?php

namespace Alyani\Subsystem\Http\Requests\Admin\HeavyUploader;

use Alyani\Subsystem\Http\Requests\Admin\WebRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class DeleteRequest extends WebRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'SID' => ['required', 'string'],
        ];
    }
}
