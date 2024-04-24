<?php

namespace Tests\Feature;

use App\Components\Passwords\Password;
use App\Components\Passwords\PasswordRulesBuilder;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\CreatesApplication;

class PasswordTest extends TestCase
{
    use CreatesApplication;

    /**
     * Test password rule is created from callback.
     */
    public function test_password_callback_creates(): void
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            // ...
        });

        $this->assertNotNull($password);
    }

    /**
     * Test password rule is created from callback.
     */
    public function test_password_rule_from_config(): void
    {
        $config = [
            'minimum' => 12,
            'maximum' => 0,
            'lowercase' => 0,
            'uppercase' => 0,
            'numbers' => 0,
            'special' => 0,
            'ascii' => true,
            'whitespaces' => false,
        ];

        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) use ($config) {
            $builder->fromConfig($config);
        });

        $this->assertNotNull($password);
    }

    /**
     * Tests password is over min length
     *
     * @return void
     */
    public function test_password_exceeds_min_length()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->min(12);
        });

        $validator = Validator::make(
            ['password' => 'aaaaaaaaaaaa'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password is under min length
     *
     * @return void
     */
    public function test_password_under_min_length()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->min(12);
        });

        $validator = Validator::make(
            ['password' => 'aaaaaaaaaa'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password exceeds max length
     *
     * @return void
     */
    public function test_password_exceeds_max_length()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->max(12);
        });

        $validator = Validator::make(
            ['password' => 'aaaaaaaaaaaaa'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password is under max length
     *
     * @return void
     */
    public function test_password_under_max_length()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->max(12);
        });

        $validator = Validator::make(
            ['password' => 'aaaaaaaaaa'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password has lowercase
     *
     * @return void
     */
    public function test_password_has_lowercase()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->lowercase(1);
        });

        $validator = Validator::make(
            ['password' => 'aaaaa123!'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password is missing lowercase
     *
     * @return void
     */
    public function test_password_missing_lowercase()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->lowercase(1);
        });

        $validator = Validator::make(
            ['password' => 'AAAAA123!'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password has uppercase
     *
     * @return void
     */
    public function test_password_has_uppercase()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->uppercase(1);
        });

        $validator = Validator::make(
            ['password' => 'AAAAAA123!'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password is missing uppercase
     *
     * @return void
     */
    public function test_password_missing_uppercase()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->uppercase(1);
        });

        $validator = Validator::make(
            ['password' => 'aaaaaa123!'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password has numbers
     *
     * @return void
     */
    public function test_password_has_numbers()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->numbers(1);
        });

        $validator = Validator::make(
            ['password' => 'AAAAAA123!'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password is missing numbers
     *
     * @return void
     */
    public function test_password_missing_numbers()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->numbers(1);
        });

        $validator = Validator::make(
            ['password' => 'aaaaaa!'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password has symbols
     *
     * @return void
     */
    public function test_password_has_symbols()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->symbols(1);
        });

        $validator = Validator::make(
            ['password' => 'AAAAAA123!'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password is missing numbers
     *
     * @return void
     */
    public function test_password_missing_symbols()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->symbols(1);
        });

        $validator = Validator::make(
            ['password' => 'aaaaaa123'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password has ASCII text
     *
     * @return void
     */
    public function test_password_has_ascii()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->ascii(true);
        });

        $validator = Validator::make(
            ['password' => 'AAAAAA123!'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password with non-ASCII text is allowed
     *
     * @return void
     */
    public function test_password_non_ascii_allowed()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->ascii(false);
        });

        $validator = Validator::make(
            ['password' => 'āǎăáâãàäå123'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password with non-ASCII text is disallowed
     *
     * @return void
     */
    public function test_password_non_ascii_disallowed()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->ascii(true);
        });

        $validator = Validator::make(
            ['password' => 'āǎăáâãàäå123'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password denies whitespaces
     *
     * @return void
     */
    public function test_password_deny_whitespaces()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->whitespaces(false);
        });

        $validator = Validator::make(
            [
                'spaces' => '1 2 3!',
                'tabs' => "1\t2\t3!",
                'newlines' => "1\r\n2\n3!",
            ],
            [
                'spaces' => $password,
                'tabs' => $password,
                'newlines' => $password,
            ]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password allows spaces
     *
     * @return void
     */
    public function test_password_allow_spaces()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->whitespaces(spaces: true);
        });

        $validator = Validator::make(
            [
                'spaces' => '1 2 3!',
                'tabs' => "1\t2\t3!",
                'newlines' => "1\r\n2\n3!",
            ],
            [
                'spaces' => $password,
                'tabs' => $password,
                'newlines' => $password,
            ]
        );

        $messages = $validator->messages();

        $this->assertFalse($messages->has('spaces'));
        $this->assertTrue($messages->has('tabs'));
        $this->assertTrue($messages->has('newlines'));
    }

    /**
     * Tests password allows tabs
     *
     * @return void
     */
    public function test_password_allow_tabs()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->whitespaces(tabs: true);
        });

        $validator = Validator::make(
            [
                'spaces' => '1 2 3!',
                'tabs' => "1\t2\t3!",
                'newlines' => "1\r\n2\n3!",
            ],
            [
                'spaces' => $password,
                'tabs' => $password,
                'newlines' => $password,
            ]
        );

        $messages = $validator->messages();

        $this->assertTrue($messages->has('spaces'));
        $this->assertFalse($messages->has('tabs'));
        $this->assertTrue($messages->has('newlines'));
    }

    /**
     * Tests password allows newlines
     *
     * @return void
     */
    public function test_password_allow_newlines()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->whitespaces(newlines: true);
        });

        $validator = Validator::make(
            [
                'spaces' => '1 2 3!',
                'tabs' => "1\t2\t3!",
                'newlines' => "1\r\n2\n3!",
            ],
            [
                'spaces' => $password,
                'tabs' => $password,
                'newlines' => $password,
            ]
        );

        $messages = $validator->messages();

        $this->assertTrue($messages->has('spaces'));
        $this->assertTrue($messages->has('tabs'));
        $this->assertFalse($messages->has('newlines'));
    }

    /**
     * Tests password is blacklisted common word.
     *
     * @return void
     */
    public function test_password_is_blacklisted_common()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->blacklists(['common-passwords']);
        });

        $validator = Validator::make(
            ['password' => 'password'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password is blacklisted common word.
     *
     * @return void
     */
    public function test_password_is_l33t_blacklisted_common()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->blacklists(['common-passwords'], substitutions: true);
        });

        $validator = Validator::make(
            ['password' => 'p@ssw0rd'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Tests password is blacklisted common word.
     *
     * @return void
     */
    public function test_password_isnt_blacklisted_common()
    {
        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) {
            $builder->blacklists(['common-passwords']);
        });

        $validator = Validator::make(
            ['password' => Str::random()],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Test config allows password.
     */
    public function test_password_config_allows_password(): void
    {
        $config = [
            'minimum' => 12,
            'maximum' => 0,
            'lowercase' => 1,
            'uppercase' => 1,
            'numbers' => 1,
            'special' => 1,
            'ascii' => true,
            'whitespaces' => false,
        ];

        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) use ($config) {
            $builder->fromConfig($config);
        });

        $validator = Validator::make(
            ['password' => 'nKZAD10Yw2OxLNFL%'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes(), $validator->messages()->first());
    }

    /**
     * Test config denies passwords.
     */
    public function test_password_config_denies_passwords(): void
    {
        $config = [
            'minimum' => 12,
            'maximum' => 20,
            'lowercase' => 1,
            'uppercase' => 1,
            'numbers' => 1,
            'special' => 1,
            'ascii' => true,
            'whitespaces' => false,
        ];

        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) use ($config) {
            $builder->fromConfig($config);
        });

        $data = [
            'minimum' => 'lnnabZK9YM!',
            'maximum' => 'G3wClMZiKvt6Qo4C4Cz!@',
            'lowercase' => 'LNNABAFZK9YM!',
            'uppercase' => 'lnnabafzk9ym!',
            'numbers' => 'lnnabZKggghYM!',
            'special' => 'lnnabZKggghYMf',
            'ascii' => 'lnnåābZKǎāùhYMf',
            'whitespaces' => 'lnna bZK9 YM!',
        ];

        $rules = [
            'minimum' => $password,
            'maximum' => $password,
            'lowercase' => $password,
            'uppercase' => $password,
            'numbers' => $password,
            'special' => $password,
            'ascii' => $password,
            'whitespaces' => $password,
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes(), $validator->messages()->first());
        $this->assertEquals(count($rules), count($validator->messages()->keys()), Arr::join(array_diff(array_keys($rules), $validator->messages()->keys()), ', '));
    }

    /**
     * Test config blacklists password.
     */
    public function test_password_config_checks_blacklist(): void
    {
        $config = [
            'blacklists' => ['blacklists' => ['common-passwords']],
        ];

        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) use ($config) {
            $builder->fromConfig($config);
        });

        $validator = Validator::make(
            ['password' => 'password123'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Test config blacklists password that has substitutions.
     */
    public function test_password_config_checks_blacklist_substitutions(): void
    {
        $config = [
            'blacklists' => [
                'blacklists' => ['common-passwords'],
                'substitutions' => true,
            ],
        ];

        $password = Password::createFromCallback(function (PasswordRulesBuilder $builder) use ($config) {
            $builder->fromConfig($config);
        });

        $validator = Validator::make(
            ['password' => 's3cr3t'],
            ['password' => $password]
        );

        $this->assertFalse($validator->passes(), $validator->messages()->first());
    }

    /**
     * Test password is validated against defaults.
     */
    public function test_password_defaults_validated(): void
    {
        $config = [
            'minimum' => 12,
            'maximum' => 0,
            'lowercase' => 1,
            'uppercase' => 1,
            'numbers' => 1,
            'special' => 1,
            'ascii' => true,
            'whitespaces' => true,
        ];

        Password::defaults(Password::createFromCallback(function (PasswordRulesBuilder $builder) use ($config) {
            $builder->fromConfig($config);
        }));

        $password = Password::default();

        $this->assertInstanceOf(Password::class, $password);

        $validator = Validator::make(
            ['password' => 'EkqZ6VEZksS21966!'],
            ['password' => $password]
        );

        $this->assertTrue($validator->passes());
    }
}
