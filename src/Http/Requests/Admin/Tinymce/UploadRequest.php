<?php

namespace Alyani\Subsystem\Http\Requests\Admin\Tinymce;

use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Alyani\Subsystem\Http\Requests\Admin\WebRequest;

class UploadRequest extends WebRequest
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
            'type' => ['required', Rule::in(array_keys($storageRules)),],
            'file' => array_merge(['required', 'file',], ($storageRules[$this->type]['validate'] ?? [])),
            'modelName' => ['required', 'string'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'modelName' => ucfirst($this->modelName)
        ]);
    }
}
