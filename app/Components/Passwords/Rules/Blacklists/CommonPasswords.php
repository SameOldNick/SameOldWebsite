<?php

namespace App\Components\Passwords\Rules\Blacklists;

use App\Components\Passwords\Contracts\Blacklist;

final class CommonPasswords implements Blacklist
{
    /*
     * A list of common passwords that password will be checked against
     * Source: https://techcult.com/most-common-passwords/
     */
    private static $commonPasswords = [
        '00000',
        '000000',
        '0000',
        '123',
        '1111',
        '1234',
        '2000',
        '5432',
        '5555',
        '6969',
        '11111',
        '11223',
        '12121',
        '12312',
        '12332',
        '12345',
        '55555',
        '65432',
        '66666',
        '111111',
        '112233',
        '121212',
        '123123',
        '123321',
        '123456',
        '11111111',
        '12345678',
        '1234567890',
        '1234567891',
        '123qw',
        '123qwe',
        '131313',
        '159753',
        '1234567',
        '098765432',
        '123456789',
        '18atcskd2',
        '1q2w3',
        '1q2w3e4',
        '1q2w3e4r5',
        '1qaz2wsx',
        '1qaz2wsx3ed',
        '3rjs1la7q',
        '555555',
        '654321',
        '666666',
        '696969',
        '777777',
        '7777777',
        '98765432',
        '987654321',
        'aa123456',
        'aaaaaa',
        'abc12',
        'abc123',
        'abcd123',
        'access',
        'admin',
        'amand',
        'amanda',
        'andre',
        'andrew',
        'animot',
        'asd',
        'asdfgh',
        'asdfghjk',
        'ashle',
        'ashley',
        'asshole',
        'austin',
        'babygir',
        'baile',
        'basebal',
        'baseball',
        'basketbal',
        'batman',
        'biteme',
        'buste',
        'buster',
        'butterfl',
        'bvttest12',
        'charli',
        'charlie',
        'cheese',
        'chegg123',
        'chelsea',
        'chocolat',
        'computer',
        'cooki',
        'dallas',
        'danie',
        'daniel',
        'drago',
        'dragon',
        'dubsmas',
        'famil',
        'fitnes',
        'flowe',
        'footbal',
        'football',
        'freedom',
        'fuck',
        'fuckme',
        'fuckyo',
        'fuckyou',
        'g_czechou',
        'george',
        'ginge',
        'ginger',
        'googl',
        'hanna',
        'harley',
        'hell',
        'hello',
        'hockey',
        'hunte',
        'hunter',
        'iloveyo',
        'iloveyou',
        'jasmin',
        'jennife',
        'jennifer',
        'jessic',
        'jessica',
        'jorda',
        'jordan',
        'joshu',
        'joshua',
        'justi',
        'killer',
        'klaster',
        'letmei',
        'letmein',
        'livetes',
        'logi',
        'love',
        'lovel',
        'madiso',
        'maggi',
        'maggie',
        'mari',
        'maste',
        'master',
        'matrix',
        'matthe',
        'matthew',
        'michae',
        'michael',
        'michell',
        'michelle',
        'monke',
        'monkey',
        'mustang',
        'mynoo',
        'nicol',
        'nicole',
        'nothin',
        'pass',
        'passwor',
        'password',
        'peppe',
        'pepper',
        'princes',
        'princess',
        'purpl',
        'pussy',
        'q1w2e3r4t5y',
        'qazwsx',
        'qwert',
        'qwerty',
        'qwerty12',
        'qwertyuio',
        'qwertyuiop',
        'ranger',
        'robert',
        'samanth',
        'secre',
        'shado',
        'shadow',
        'shoppin',
        'socce',
        'soccer',
        'sophi',
        'starwars',
        'summe',
        'summer',
        'sunshin',
        'sunshine',
        'superma',
        'superman',
        'taylo',
        'taylor',
        'tes',
        'test',
        'thoma',
        'thomas',
        'thunder',
        'tigge',
        'tigger',
        'trustno1',
        'whateve',
        'yankees',
        'zinc',
        'zxcvbn',
        'zxcvbnm',
    ];

    protected readonly string $regexPattern;

    /**
     * Initializes CommonPasswords blacklist.
     */
    public function __construct()
    {
        $this->regexPattern = $this->generateRegexPattern($this->getBlacklist());
    }

    /**
     * @inheritDoc
     */
    public function isBlacklisted(#[\SensitiveParameter] string $value): bool
    {
        return preg_match("/{$this->regexPattern}/i", $value);
    }

    /**
     * Generates regex pattern to run password through.
     *
     * @param array $commonPasswords
     * @return string
     */
    protected function generateRegexPattern(array $commonPasswords): string
    {
        // Escape special characters in each password
        $escapedPasswords = array_map('preg_quote', $commonPasswords);

        // Join escaped passwords into a single regex pattern
        $regexPattern = implode('|', $escapedPasswords);

        // Enclose the pattern within word boundaries
        $regexPattern = '(?:'.$regexPattern.')';

        return $regexPattern;
    }

    /**
     * Gets black list.
     *
     * @return array
     */
    public function getBlacklist(): array
    {
        return static::$commonPasswords;
    }
}
