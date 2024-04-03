<?php

namespace App\Providers;

use App\Rules;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationServiceProvider as ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->getRules() as $name => $class) {
            $this->extendRule($name, new $class);
        }
    }

    /**
     * Gets the rules to provide implicitly
     *
     * @return array
     */
    protected function getRules()
    {
        return [];
    }

    protected function extendRule($name, Rule $rule)
    {
        Validator::extend($name, function ($attribute, $value, $parameters, $validator) use ($rule) {
            return $rule->passes($attribute, $value);
        }, $rule->message());
    }
}
