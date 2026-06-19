<?php

namespace Alyani\Subsystem\Http\Requests\Admin\UserManageBalance;

use Alyani\Subsystem\Http\Requests\Admin\WebRequest;
use Illuminate\Validation\Rule;
use Alyani\Subsystem\Enums\Currency;

class DecreaseRequest extends WebRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', Rule::in(Currency::values(withDisplayValues: true))],
            'description' => ['required', 'string', 'max:255'],
        ];
    }

    /**
    * Prepare the data for validation.
    *
    * @return void
    */
    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => (int) replacePersianDigistWithEnglish(str_replace(',', '', $this->amount)),
        ]);
    }
}
