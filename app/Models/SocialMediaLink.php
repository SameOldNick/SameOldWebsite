<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $link
 * @property-read ?string $platform
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class SocialMediaLink extends Model
{
    use HasFactory;

    public static $platforms = [
        'facebook' => [
            'regex' => '/^(https?:\/\/)?(www\.)?(facebook\.com)/i',
            'icon' => 'fab-facebook',
        ],
        'twitter' => [
            'regex' => '/^(https?:\/\/)?(www\.)?(twitter\.com|x\.com)/i',
            'icon' => 'fab-x-twitter',
        ],
        'linkedin' => [
            'regex' => '/^(https?:\/\/)?(www\.)?(linkedin\.com)/i',
            'icon' => 'fab-linkedin',
        ],
        'github' => [
            'regex' => '/^(https?:\/\/)?(www\.)?(github\.com)/i',
            'icon' => 'fab-github',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['link'];

    /**
     * Get the social media platform
     */
    protected function platform(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                foreach (static::$platforms as $id => $options) {
                    if (preg_match($options['regex'], $attributes['link'])) {
                        return $id;
                    }
                }

                return null;
            },
        );
    }

    /**
     * Get the social media platform icon.
     */
    protected function icon(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                $id = $this->platform;

                if (is_null($id)) {
                    return null;
                }

                return static::$platforms[$id]['icon'];
            },
        );
    }
}
