<?php

namespace App\Traits\Rules;

use Illuminate\Support\Facades\Validator;

trait AggregateValidators
{
    /**
     * Validator message to use.
     *
     * @var string
     */
    protected $message = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $data = [$attribute => $value];

        $validator = Validator::make($data, [$attribute => $this->rules()], $this->messages());

        $validator->after(function ($validator) {
            $this->message = $validator->messages()->first();
        });

        return ! $validator->fails();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Gets validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Gets messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
