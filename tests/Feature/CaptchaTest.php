<?php

namespace Tests\Feature;

use App\Components\Captcha\Drivers\Recaptcha\Testing\DriverBuilder;
use App\Components\Captcha\Drivers\Recaptcha\UserResponse;
use App\Components\Captcha\Drivers\Recaptcha\Verifier;
use App\Components\Captcha\Exceptions\VerificationException;
use App\Components\Captcha\Facades\Captcha;
use App\Components\Captcha\Rules\CaptchaRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CaptchaTest extends TestCase
{
    use WithFaker;

    /**
     * Tests a captcha response with a valid recaptcha response.
     */
    public function test_valid_recaptcha(): void
    {
        Captcha::fake(DriverBuilder::create(function (DriverBuilder $builder) {
            $builder->withValidResponse();
        }));

        try {
            Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: '.$e->getMessage());
        }
    }

    /**
     * Tests an invalid recaptcha response with an invalid recaptcha response.
     */
    public function test_invalid_recaptcha_no_errorcode(): void
    {
        Captcha::fake(DriverBuilder::create(function (DriverBuilder $builder) {
            $builder->withInvalidResponse();
        }));

        $this->expectException(VerificationException::class);
        $this->expectExceptionMessage('Captcha verification failed');

        Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));
    }

    /**
     * Tests an invalid recaptcha response with a random error code.
     */
    public function test_invalid_recaptcha_random_errorcode(): void
    {
        Captcha::fake(DriverBuilder::create(function (DriverBuilder $builder) {
            $builder->withRandomErrorCodes();
        }));

        try {
            Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));

            $this->fail('Exception '.VerificationException::class.' not thrown.');
        } catch (VerificationException $e) {
            $this->assertTrue(true);
            $this->assertMatchesRegularExpression('/Captcha verification failed: .+/', $e->getMessage());
            $this->assertMatchesRegularExpression(
                '/'.implode('|', Arr::map(Verifier::$errorCodes, fn ($message) => preg_quote($message))).'/',
                $e->getMessage()
            );
        }
    }

    /**
     * Tests an invalid recaptcha response with multiple random error codes.
     */
    public function test_invalid_recaptcha_random_errorcodes(): void
    {
        Captcha::fake(DriverBuilder::create(function (DriverBuilder $builder) {
            $builder->withRandomErrorCodes(5);
        }));

        try {
            Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));

            $this->fail('Exception '.VerificationException::class.' not thrown.');
        } catch (VerificationException $e) {
            $this->assertTrue(true);
            $this->assertMatchesRegularExpression('/Captcha verification failed: .+/', $e->getMessage());
            $this->assertMatchesRegularExpression(
                '/'.implode('|', Arr::map(Verifier::$errorCodes, fn ($message) => preg_quote($message))).'/',
                $e->getMessage()
            );
        }
    }

    /**
     * Tests an invalid recaptcha response with a low score.
     */
    public function test_invalid_recaptcha_score_too_low(): void
    {
        Captcha::fake(DriverBuilder::create(function (DriverBuilder $builder) {
            $builder->withValidResponse(score: 0.1);
        }));

        try {
            Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));

            $this->fail('Exception '.VerificationException::class.' not thrown.');
        } catch (VerificationException $e) {
            $this->assertTrue(true);
            $this->assertEquals('Captcha verification failed: score too low', $e->getMessage());
        }
    }

    /**
     * Tests an invalid recaptcha response with a high score.
     */
    public function test_recaptcha_http_request_success(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: '.$e->getMessage());
        }

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an invalid recaptcha response with a high score.
     */
    public function test_recaptcha_http_request_fail(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.9,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));

            $this->fail('Exception '.VerificationException::class.' not thrown.');
        } catch (VerificationException $e) {
            $this->assertTrue(true);
        }

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests a recaptcha response with a low score.
     */
    public function test_recaptcha_http_request_low_score(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));

            $this->fail('Exception '.VerificationException::class.' not thrown.');
        } catch (VerificationException $e) {
            $this->assertTrue(true);
        }

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests a recaptcha response with a 500 server error.
     */
    public function test_recaptcha_http_request_server_error(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([], 500),
        ]);

        try {
            Captcha::validate(new UserResponse($this->faker->sha256, $this->faker->ipv4));

            $this->fail('Exception '.VerificationException::class.' not thrown.');
        } catch (VerificationException $e) {
            $this->assertTrue(true);
        }

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests captcha rule passes.
     */
    public function test_recaptcha_rule_http_passes(): void
    {
        Captcha::fake(DriverBuilder::create(function (DriverBuilder $builder) {
            $builder->withValidResponse();
        }));

        try {
            Validator::make(['g-recaptcha-response' => $this->faker->sha256], [
                'g-recaptcha-response' => CaptchaRule::required('recaptcha'),
            ])->validate();

            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('Validation exception thrown: '.$e->getMessage());
        }
    }

    /**
     * Tests captcha rule fails.
     */
    public function test_recaptcha_rule_http_fails(): void
    {
        Captcha::fake(DriverBuilder::create(function (DriverBuilder $builder) {
            $builder->withInvalidResponse();
        }));

        $this->expectException(ValidationException::class);

        Validator::make(['g-recaptcha-response' => $this->faker->sha256], [
            'g-recaptcha-response' => CaptchaRule::required('recaptcha'),
        ])->validate();
    }

    /**
     * Tests captcha rule passes with generated response.
     */
    public function test_recaptcha_rule_response_generated_passes(): void
    {
        Captcha::fake();

        try {
            Validator::make(['g-recaptcha-response' => CaptchaRule::validResponse()], [
                'g-recaptcha-response' => CaptchaRule::required('recaptcha'),
            ])->validate();

            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('Validation exception thrown: '.$e->getMessage());
        }
    }

    /**
     * Tests captcha rule fails with a random response.
     */
    public function test_recaptcha_rule_response_any_fails(): void
    {
        Captcha::fake();

        $this->expectException(ValidationException::class);

        Validator::make(['g-recaptcha-response' => $this->faker->uuid], [
            'g-recaptcha-response' => CaptchaRule::required('recaptcha'),
        ])->validate();
    }

    /**
     * Tests captcha rule fails with an invalid response.
     */
    public function test_recaptcha_rule_response_generated_fails(): void
    {
        Captcha::fake();

        $this->expectException(ValidationException::class);

        Validator::make(['g-recaptcha-response' => CaptchaRule::invalidResponse()], [
            'g-recaptcha-response' => CaptchaRule::required('recaptcha'),
        ])->validate();
    }

    /**
     * Tests an IPv4 address is not excluded from the verification.
     */
    public function test_recaptcha_include_exact_ipv4(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = $this->faker->unique()->ipv4;

            config(['captcha.drivers.recaptcha.exclude_ips' => [$this->faker->unique()->ipv4]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->fail('Recaptcha verification passed: IP address is not excluded.');
        } catch (VerificationException $e) {
            $this->assertTrue(true);
        }

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an IPv6 address is not excluded from the verification.
     */
    public function test_recaptcha_include_exact_ipv6(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = $this->faker->unique()->ipv6;

            config(['captcha.drivers.recaptcha.exclude_ips' => [$this->faker->unique()->ipv6]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->fail('Recaptcha verification passed: IP address is not excluded.');
        } catch (VerificationException $e) {
            $this->assertTrue(true);
        }

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an IPv4 address is excluded from the verification.
     */
    public function test_recaptcha_exclude_exact_ipv4(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = $this->faker->ipv4;

            config(['captcha.drivers.recaptcha.exclude_ips' => [$ip]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: ' . $e->getMessage());
        }

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an IPv6 address is excluded from the verification.
     */
    public function test_recaptcha_exclude_exact_ipv6(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = $this->faker->ipv6;

            config(['captcha.drivers.recaptcha.exclude_ips' => [$ip]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: ' . $e->getMessage());
        }

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an IPv4 address is excluded from the verification.
     */
    public function test_recaptcha_exclude_wildcard_ipv4(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = '192.168.100.1';
            $mask = '192.168.*.*';

            config(['captcha.drivers.recaptcha.exclude_ips' => [$mask]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: ' . $e->getMessage());
        }

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an IPv6 address is excluded from the verification.
     */
    public function test_recaptcha_exclude_wildcard_ipv6(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = 'dbf0:7456:add0:d165:7bd0:65ba:b906:530d';
            $mask = 'dbf0:7456:add0:d165:7bd0:65ba:*:*';

            config(['captcha.drivers.recaptcha.exclude_ips' => [$mask]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: ' . $e->getMessage());
        }

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an IPv4 address is excluded from the verification.
     */
    public function test_recaptcha_exclude_cidr_range_ipv4(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = '192.168.100.1';
            $mask = '192.168.0.0/16';

            config(['captcha.drivers.recaptcha.exclude_ips' => [$mask]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: ' . $e->getMessage());
        }

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an IPv6 address is excluded from the verification.
     */
    public function test_recaptcha_exclude_shrunk_cidr_range_ipv6(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = 'dbf0:7456:add0:d165:7bd0:65ba:b906:530d';
            $mask = 'dbf0:7456:add0:d165:7bd0::/64';

            config(['captcha.drivers.recaptcha.exclude_ips' => [$mask]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: ' . $e->getMessage());
        }

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }

    /**
     * Tests an IPv6 address is excluded from the verification.
     */
    public function test_recaptcha_exclude_expanded_cidr_range_ipv6(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.1,
                'action' => 'homepage',
                'challenge_ts' => '2021-09-01T00:00:00Z',
                'hostname' => 'example.com',
                'error-codes' => [],
            ]),
        ]);

        try {
            $ip = 'dbf0:7456:add0:d165:7bd0:65ba:b906:530d';
            $mask = 'dbf0:7456:add0:d165:7bd0:0000:0000:0000/64';

            config(['captcha.drivers.recaptcha.exclude_ips' => [$mask]]);

            Captcha::validate(new UserResponse($this->faker->sha256, $ip));

            $this->assertTrue(true);
        } catch (VerificationException $e) {
            $this->fail('Recaptcha verification failed: ' . $e->getMessage());
        }

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify';
        });
    }
}
