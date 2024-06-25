<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $icon
 * @property string $skill
 * @property ?\DateTimeInterface $created_at
 * @property ?\DateTimeInterface $updated_at
 *
 * @method static \Database\Factories\SkillFactory factory($count = null, $state = [])
 */
class Skill extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['icon', 'skill'];
}
