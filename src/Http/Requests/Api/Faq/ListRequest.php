<?php

namespace Alyani\Subsystem\Http\Requests\Api\Faq;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class ListRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'relatedTo' => ['nullable', 'string'],
            'relatedID' => ['nullable', 'integer'],
        ];
    }
}
