<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anjuman;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;

class AnjumanController extends Controller
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

    public function index(Request $request)
    {
        $query = Anjuman::query();
        if ($request->city) {
            $query->whereRaw('LOWER(city) = ?', [strtolower($request->city)]);
        }
        return response()->json($query->orderBy('name')->get());
    }

    public function show($id)
    {
        $anjuman = Anjuman::findOrFail($id);
        return response()->json($anjuman);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'city' => 'required|string',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $res = $this->cloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'anjumans', 'resource_type' => 'image']
            );
            $imageUrl = $res['secure_url'];
        }

        $anjuman = Anjuman::create([
            'name'        => $request->name,
            'city'        => $request->city,
            'bio'         => $request->bio,
            'image_url'   => $imageUrl,
            'is_verified' => $request->boolean('is_verified'),
        ]);

        return response()->json($anjuman, 201);
    }

    public function update(Request $request, $id)
    {
        $anjuman = Anjuman::findOrFail($id);

        $imageUrl = $anjuman->image_url;
        if ($request->hasFile('image')) {
            $res = $this->cloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'anjumans', 'resource_type' => 'image']
            );
            $imageUrl = $res['secure_url'];
        }

        $anjuman->update([
            'name'        => $request->name ?? $anjuman->name,
            'city'        => $request->city ?? $anjuman->city,
            'bio'         => $request->bio ?? $anjuman->bio,
            'image_url'   => $imageUrl,
            'is_verified' => $request->has('is_verified') ? $request->boolean('is_verified') : $anjuman->is_verified,
        ]);

        return response()->json($anjuman);
    }

    public function destroy($id)
    {
        Anjuman::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
