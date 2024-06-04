<?php

namespace App\Components\Passwords\Concerns;

use App\Components\Passwords\Contracts\Rule;
use App\Components\Passwords\PasswordRules;
use App\Components\Passwords\Rules;

trait GeneratesFromPasswordRules
{
    /**
     * Generates options from password rules.
     *
     * @return $this
     */
    public function generateFromRules(PasswordRules $passwordRules)
    {
        foreach ($passwordRules->getRules() as $passwordRule) {
            if ($this->hasMappedOption($passwordRule)) {
                $this->mapToOption($passwordRule);
            } elseif ($this->configuresGenerator($passwordRule)) {
                $this->configureGenerator($passwordRule);
            }
        }

        return $this;
    }

    /**
     * Gets rule mappings
     *
     * @return array<class-string, callable>
     */
    protected function getRuleMappings()
    {
        return [
            Rules\MinLength::class => fn (Rules\MinLength $rule) => $this->minimumLength($rule->min),
            Rules\MaxLength::class => fn (Rules\MaxLength $rule) => $this->maximumLength($rule->max),
            Rules\Lowercase::class => fn (Rules\Lowercase $rule) => $this->requiresLowerCase($rule->value),
            Rules\Uppercase::class => fn (Rules\Uppercase $rule) => $this->requiresUpperCase($rule->value),
            Rules\Numbers::class => fn (Rules\Numbers $rule) => $this->requiresNumbers($rule->value),
            Rules\SpecialSymbols::class => fn (Rules\SpecialSymbols $rule) => $this->requiresSpecialSymbols($rule->value),
            Rules\Ascii::class => fn (Rules\Ascii $rule) => $this->onlyAscii($rule->value),
            Rules\Whitespaces::class => fn (Rules\Whitespaces $rule) => $this->whitespaces([
                'spaces' => $rule->spaces,
                'tabs' => $rule->tabs,
                'newlines' => $rule->newlines,
            ]),
        ];
    }

    /**
     * Gets rule mapping
     *
     * @param  class-string|Rule  $passwordRule
     * @return callable|null
     */
    protected function getRuleMapping($passwordRule)
    {
        $class = is_object($passwordRule) ? get_class($passwordRule) : (string) $passwordRule;

        return isset($this->getRuleMappings()[$class]) ? $this->getRuleMappings()[$class] : null;
    }

    /**
     * Check if rule can be mapped.
     *
     * @return bool
     */
    protected function hasMappedOption(Rule $passwordRule)
    {
        return ! is_null($this->getRuleMapping($passwordRule));
    }

    /**
     * Maps rule to generator option
     *
     * @return void
     */
    protected function mapToOption(Rule $passwordRule)
    {
        call_user_func($this->getRuleMapping($passwordRule), $passwordRule);
    }

    /**
     * Checks if rule cna configure generator options
     *
     * @return bool
     */
    protected function configuresGenerator(Rule $rule)
    {
        return method_exists($rule, 'configureGenerator');
    }

    /**
     * Configures generator options using rule
     *
     * @return void
     */
    protected function configureGenerator(Rule $rule)
    {
        call_user_func([$rule, 'configureGenerator'], $this);
    }

    /**
     * Creates builder from password rules
     */
    public static function createFrom(PasswordRules $passwordRules): static
    {
        return (new static)->generateFromRules($passwordRules);
    }
}
