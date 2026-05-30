<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    private function cloudinary(): Cloudinary
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $query = Track::with('reciter');

        if ($request->category) $query->where('category', $request->category);
        if ($request->language) $query->where('language', $request->language);
        if ($request->occasion) $query->where('occasion', $request->occasion);
        if ($request->reciter_id) $query->where('reciter_id', $request->reciter_id);
        if ($request->featured) $query->where('is_featured', true);
        if ($request->search) $query->where('title', 'like', $request->search . '%');

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function show($id)
    {
        return response()->json(Track::with('reciter')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'category' => 'required|in:dua,noha,manqabat,naat,ziyarat,kids,tarana',
        ]);

        $data = $request->only([
            'title', 'category', 'reciter_id', 'reciter_name',
            'language', 'occasion', 'duration', 'is_featured', 'lyrics',
        ]);

        if ($request->hasFile('audio')) {
            $result = $this->cloudinary()->uploadApi()->upload(
                $request->file('audio')->getRealPath(),
                ['folder' => 'karbalaconnect/tracks/' . $request->category, 'resource_type' => 'video']
            );
            $data['audio_url'] = $result['secure_url'];
        }

        if ($request->hasFile('image')) {
            $result = $this->cloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'karbalaconnect/covers', 'resource_type' => 'image']
            );
            $data['image_url'] = $result['secure_url'];
        }

        return response()->json(Track::create($data)->load('reciter'), 201);
    }

    public function update(Request $request, $id)
    {
        $track = Track::findOrFail($id);

        $data = $request->only([
            'title', 'category', 'reciter_id', 'reciter_name',
            'language', 'occasion', 'duration', 'is_featured', 'lyrics',
        ]);

        if ($request->hasFile('audio')) {
            $result = $this->cloudinary()->uploadApi()->upload(
                $request->file('audio')->getRealPath(),
                ['folder' => 'karbalaconnect/tracks/' . ($data['category'] ?? $track->category), 'resource_type' => 'video']
            );
            $data['audio_url'] = $result['secure_url'];
        }

        if ($request->hasFile('image')) {
            $result = $this->cloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'karbalaconnect/covers', 'resource_type' => 'image']
            );
            $data['image_url'] = $result['secure_url'];
        }

        $track->update($data);
        return response()->json($track->load('reciter'));
    }

    public function destroy($id)
    {
        Track::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function incrementPlayCount($id)
    {
        $track = Track::findOrFail($id);
        $track->increment('play_count');
        return response()->json(['play_count' => $track->play_count]);
    }
}
