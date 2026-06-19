<?php

namespace Alyani\Subsystem\Http\Requests\Admin\ArticleCategory;

use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Enums\Language;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
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
            'title' => ['required', 'string', 'max:255', Rule::unique('article_categories', 'title')],
            'slug' => ['required', 'string', 'max:255', Rule::unique('article_categories', 'slug'), 'english_only'],
            'description' => ['nullable', 'string', 'max:65530'],
            'sort_order' => ['required', 'integer', 'min:1','max:32000'],
            'photo' => ['nullable', 'image',] + Config::get('subsystem.storage.image.validate'), // حداکثر 2MB
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:65530'],
            'meta_keyword' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(ActivationStatus::values())],
            'language' => ['required', Rule::in(Language::values())],
        ];
    }
}
