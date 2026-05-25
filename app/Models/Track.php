<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'title', 'category', 'reciter_id', 'reciter_name', 'language',
        'occasion', 'audio_url', 'image_url', 'duration', 'play_count',
        'is_featured', 'lyrics',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'duration' => 'integer',
        'play_count' => 'integer',
    ];

    public function reciter()
    {
        return $this->belongsTo(Reciter::class);
    }
}
