<?php

namespace App\Components\Passwords;

use App\Components\Passwords\Contracts\Rule;
use App\Components\Passwords\Rules\CustomRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

final class PasswordRulesBuilder
{
    use Macroable {
        Macroable::__call as callMacro;
    }

    /**
     * Password rules
     *
     * @var Rule[]
     */
    private $rules = [];

    /**
     * Rule mappings
     *
     * @var array<string, class-string>
     */
    private $ruleMappings = [];

    public function __construct()
    {
        $this->ruleMappings = $this->getPredefinedRuleMappings();
    }

    /**
     * Adds rule
     *
     * @param Rule $rule
     * @return static
     */
    public function addRule(Rule $rule): static
    {
        array_push($this->rules, $rule);

        return $this;
    }

    /**
     * Populates rules from configuration array.
     *
     * @param array $config
     * @return static
     */
    public function fromConfig(array $config): static
    {
        foreach ($config as $key => $value) {
            if ($this->isMappedRule($key)) {
                $this->addRule($this->getMappedRule($key, Arr::wrap($value)));
            } elseif (isset($value['class']) && is_subclass_of($value['class'], Rule::class)) {
                $this->addRule(new CustomRule(function (Password $password) use ($value) {
                    $rule = App::make($value['class'], compact('value'));

                    return $rule->configure($password);
                }));
            } elseif (isset($value['callback']) && is_callable($value['callback'])) {
                $this->addRule(new CustomRule($value['callback']));
            }
        }

        return $this;
    }

    /**
     * Gets the password rules.
     *
     * @return PasswordRules
     */
    public function getRules(): PasswordRules
    {
        return new PasswordRules($this->rules);
    }

    /**
     * Adds rule mapping
     *
     * @param string $key
     * @param string $class
     * @return $this
     */
    public function addRuleMapping(string $key, string $class): static
    {
        $this->ruleMappings[$key] = $class;

        return $this;
    }

    /**
     * Gets predefined rule mappings.
     *
     * @return array
     */
    protected function getPredefinedRuleMappings(): array
    {
        return [
            'min' => Rules\MinLength::class,
            'minimum' => Rules\MinLength::class,
            'max' => Rules\MaxLength::class,
            'maximum' => Rules\MaxLength::class,
            'lowercase' => Rules\Lowercase::class,
            'uppercase' => Rules\Uppercase::class,
            'numbers' => Rules\Numbers::class,
            'special' => Rules\SpecialSymbols::class,
            'symbols' => Rules\SpecialSymbols::class,
            'ascii' => Rules\Ascii::class,
            'whitespaces' => Rules\Whitespaces::class,
            'blacklists' => Rules\Blacklist::class,
        ];
    }

    /**
     * Checks if key is mapped to Rule class.
     *
     * @param string $key
     * @return bool
     */
    protected function isMappedRule($key): bool
    {
        return isset($this->ruleMappings[$key]);
    }

    /**
     * Gets mapped rule
     *
     * @param string $key
     * @param array $value
     * @return Rule
     */
    protected function getMappedRule($key, array $value): Rule
    {
        $class = $this->ruleMappings[$key];

        return new $class(...$value);
    }

    /**
     * Adds rule based on method name and parameters.
     *
     * @param string $method
     * @param array $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if ($this->isMappedRule(Str::kebab($method))) {
            return $this->addRule($this->getMappedRule(Str::kebab($method), $parameters));
        } else {
            return $this->callMacro($method, $parameters);
        }
    }
}
