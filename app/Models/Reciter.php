<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reciter extends Model
{
    protected $fillable = ['name', 'bio', 'image_url', 'categories', 'languages', 'total_tracks', 'is_verified'];

    protected $casts = [
        'categories' => 'array',
        'languages' => 'array',
        'is_verified' => 'boolean',
    ];

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }
}
