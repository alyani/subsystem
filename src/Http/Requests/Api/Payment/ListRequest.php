<?php

namespace Alyani\Subsystem\Http\Requests\Api\Payment;

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
            'page' => ['nullable', 'integer', 'gt:0'],
            'items_per_page' => ['nullable', 'integer', 'gt:0', Rule::in(Config::get('subsystem.availableItemsPerPage'))],
        ];
    }
}
