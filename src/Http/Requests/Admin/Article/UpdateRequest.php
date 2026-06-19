<?php

namespace Alyani\Subsystem\Http\Requests\Admin\Article;

use Alyani\Subsystem\Enums\Language;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;

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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($this->article->id), 'english_only'],
            'introduction' => ['required', 'string', 'max:5000'],
            'content' => ['required', 'string', 'max:65350'],
            'poster' => array_merge(['nullable', 'image'], Config::get('subsystem.storage.image.validate')),
            'reading_time' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:65530'],
            'meta_keyword' => ['nullable', 'string', 'max:255'],
            'articleCategories' => ['required', 'array', 'min:1'],
            'language' => ['required', Rule::in(Language::values())],
        ];
    }
}
