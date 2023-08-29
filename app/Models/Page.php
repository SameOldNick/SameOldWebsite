<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['page'];

    /**
     * Gets meta data for this page.
     *
     * @return mixed
     */
    public function metaData()
    {
        return $this->hasMany(PageMetaData::class);
    }
}
