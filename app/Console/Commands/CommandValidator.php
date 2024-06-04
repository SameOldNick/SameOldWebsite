<?php

namespace App\Console\Commands;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\ValidationException;

trait CommandValidator
{
    /**
     * Validates rules against specified arguments and options.
     *
     * @param [mixed] ...$params Any extra parameters to make validator.
     * @return void
     */
    protected function validate(array $rules, ...$params)
    {
        return $this->validateAndOutput($this->arguments() + $this->options(), $rules, ...$params);
    }

    protected function validateAndOutput($input, $rules, ...$params)
    {
        $validator = app('validator')->make($input, $rules, ...$params);

        try {
            $validator->validate();
        } catch (ValidationException $e) {
            $this->outputErrors($e->validator);
        }

        return $validator;
    }

    protected function outputErrors(ValidatorContract $validator)
    {
        if (! $validator->fails()) {
            return;
        }

        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }
    }
}
