<?php

namespace Alyani\Subsystem\Http\Requests\Api\Article;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class RelatedRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'gt:0'],
            'itemsPerPage' => ['nullable', 'integer', 'gt:0', Rule::in(Config::get('subsystem.availableItemsPerPage'))],
        ];
    }
}
