<?php

namespace App\Components\Passwords;

use Closure;

final class PasswordFactory {
    /**
     * Initializes PasswordFactory instance.
     *
     * @param PasswordRules $rules
     */
    public function __construct(
        protected readonly PasswordRules $rules
    )
    {
    }

    /**
     * Creates Password instance
     *
     * @return Password
     */
    public function create(): Password {
        $password = new Password;

        foreach ($this->getRules() as $rule) {
            $password = $rule->isEnabled() ? $rule->configure($password) : $password;
        }

        return $password;
    }

    /**
     * Creates callback that creates Password instance when called.
     *
     * @return callable
     */
    public function createPasswordCallback(): callable {
        return fn() => $this->create();
    }

    /**
     * Gets the password rules.
     *
     * @return Contracts\Rule[]
     */
    protected function getRules() {
        return $this->rules->getRules();
    }

    /**
     * Creates PasswordFactory with rules set from callback.
     *
     * @param Closure $callback Called with PasswordRules as parameter
     * @return static
     */
    public static function createFactory(Closure $callback): static {
        $passwordRules = new PasswordRules;

        $callback($passwordRules);

        return new static($passwordRules);
    }

    /**
     * Creates Password instance with rules set from callback.
     *
     * @param Closure $callback Called with PasswordRules as parameter
     * @return Password
     */
    public static function createPassword(Closure $callback): Password {
        return static::createFactory($callback)->create();
    }

    /**
     * Creates callback that when called, creates Password instance with rules set from callback.
     *
     * @param Closure $callback Called with PasswordRules as parameter
     * @return Closure
     */
    public static function createPasswordLazy(Closure $callback): Closure {
        return fn() => static::createPassword($callback);
    }
}
