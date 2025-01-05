<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use function Safe\preg_match;

/**
 * @property string $input
 * @property string $value
 */
class ContactBlacklist extends Model
{
    protected $table = 'contact_blacklist';

    protected $fillable = ['input', 'value'];
    protected $appends = ['type'];

    public function isRegexPattern()
    {
        try {
            preg_match($this->value, '');

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function matches(string $value, bool $ignoreCase = false): bool
    {
        $pattern = $this->isRegexPattern() ? $this->value : '/^' . preg_quote($this->value) . '$/';

        return preg_match($ignoreCase ? Str::lower($pattern) : $pattern, $value) > 0;
    }

    protected function type(): Attribute
    {
        return Attribute::get(fn() => $this->isRegexPattern() ? 'regex' : 'static');
    }
}
