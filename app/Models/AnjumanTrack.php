<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnjumanTrack extends Model
{
    protected $fillable = ['anjuman_id', 'title', 'audio_url', 'image_url', 'duration', 'play_count', 'occasion'];

    public function anjuman()
    {
        return $this->belongsTo(Anjuman::class);
    }
}
