<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anjuman;
use App\Models\AnjumanTrack;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;

class AnjumanTrackController extends Controller
{
    private function cloudinary(): Cloudinary
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key'    => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
        ]);
    }

    public function index($anjumanId)
    {
        $tracks = AnjumanTrack::where('anjuman_id', $anjumanId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($tracks);
    }

    public function store(Request $request, $anjumanId)
    {
        $anjuman = Anjuman::findOrFail($anjumanId);

        $request->validate([
            'title' => 'required|string',
            'audio' => 'required|file',
        ]);

        $audioRes = $this->cloudinary()->uploadApi()->upload(
            $request->file('audio')->getRealPath(),
            ['folder' => 'anjuman_tracks', 'resource_type' => 'video']
        );
        $audioUrl = $audioRes['secure_url'];
        $duration = isset($audioRes['duration'])
            ? gmdate('i:s', (int) $audioRes['duration'])
            : null;

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imgRes = $this->cloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'anjuman_tracks', 'resource_type' => 'image']
            );
            $imageUrl = $imgRes['secure_url'];
        }

        $track = AnjumanTrack::create([
            'anjuman_id' => $anjumanId,
            'title'      => $request->title,
            'audio_url'  => $audioUrl,
            'image_url'  => $imageUrl,
            'duration'   => $duration,
            'occasion'   => $request->occasion,
        ]);

        $anjuman->increment('total_tracks');

        return response()->json($track, 201);
    }

    public function destroy($id)
    {
        $track = AnjumanTrack::findOrFail($id);
        $track->anjuman->decrement('total_tracks');
        $track->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
