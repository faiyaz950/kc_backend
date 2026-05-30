<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reciter;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;

class ReciterController extends Controller
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
        $query = Reciter::withCount('tracks');

        if ($request->category) {
            $query->whereJsonContains('categories', $request->category);
        }

        return response()->json($query->orderBy('name')->get());
    }

    public function show($id)
    {
        return response()->json(
            Reciter::withCount('tracks')->with('tracks')->findOrFail($id)
        );
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $data = $request->only(['name', 'bio', 'total_tracks', 'is_verified']);
        $data['categories'] = json_decode($request->categories ?? '[]', true);
        $data['languages'] = json_decode($request->languages ?? '[]', true);

        if ($request->hasFile('image')) {
            $result = $this->cloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'karbalaconnect/reciters', 'resource_type' => 'image']
            );
            $data['image_url'] = $result['secure_url'];
        }

        $reciter = Reciter::create($data);
        return response()->json($reciter, 201);
    }

    public function update(Request $request, $id)
    {
        $reciter = Reciter::findOrFail($id);

        $data = $request->only(['name', 'bio', 'total_tracks', 'is_verified']);
        if ($request->has('categories')) {
            $data['categories'] = json_decode($request->categories, true);
        }
        if ($request->has('languages')) {
            $data['languages'] = json_decode($request->languages, true);
        }

        if ($request->hasFile('image')) {
            $result = $this->cloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'karbalaconnect/reciters', 'resource_type' => 'image']
            );
            $data['image_url'] = $result['secure_url'];
        }

        $reciter->update($data);
        return response()->json($reciter);
    }

    public function destroy($id)
    {
        Reciter::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
