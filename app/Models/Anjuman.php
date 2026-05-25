<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anjuman extends Model
{
    protected $table = 'anjumans';

    protected $fillable = ['name', 'city', 'bio', 'image_url', 'is_verified', 'total_tracks'];

    protected $casts = ['is_verified' => 'boolean'];

    public function tracks()
    {
        return $this->hasMany(AnjumanTrack::class);
    }
}
