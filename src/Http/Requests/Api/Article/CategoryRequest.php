<?php

namespace Alyani\Subsystem\Http\Requests\Api\Article;

use Alyani\Subsystem\Http\Requests\Api\ApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CategoryRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255'],
        ];
    }
}
