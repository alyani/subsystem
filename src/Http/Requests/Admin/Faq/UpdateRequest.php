<?php

namespace Alyani\Subsystem\Http\Requests\Admin\Faq;

use Alyani\Subsystem\Enums\Language;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateRequest extends WebRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:1000', 'max:65530'],
            'answer' => ['required', 'string', 'max:65530'],
            'category_id' => ['nullable', 'exists:faqsCategories,ID'],
            'faqCategoryID' => ['nullable', 'exists:faqsCategories,ID'],
            'sort_order' => ['required', 'integer', 'min:1', 'max:32000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:65530'],
            'meta_keyword' => ['nullable', 'string', 'max:255'],
            'language' => $this->faqCategoryID ? ['nullable'] : ['required', Rule::in(Language::values())],
        ];
    }
}
