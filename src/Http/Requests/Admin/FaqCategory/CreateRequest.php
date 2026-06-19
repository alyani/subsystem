<?php

namespace Alyani\Subsystem\Http\Requests\Admin\FaqCategory;

use Alyani\Subsystem\Enums\Language;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class CreateRequest extends WebRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'english_only', Rule::unique('faqsCategories', 'slug')->where(function ($query) {
                $query->where('language', $this->language);
            })],
            'sort_order' => ['required', 'integer', 'min:1', 'max:32000'],
            'language' => ['required', Rule::in(Language::values())],
        ];
    }
}
