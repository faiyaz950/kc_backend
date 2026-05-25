<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'favorites' => [],
            'recently_played' => [],
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('app')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email ya password galat hai.'],
            ]);
        }

        $user->tokens()->delete();

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('app')->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $request->validate(['name' => 'sometimes|string|max:255']);
        $request->user()->update($request->only('name'));
        return response()->json($request->user());
    }

    public function addFavorite(Request $request, $trackId)
    {
        $user = $request->user();
        $favorites = $user->favorites ?? [];
        if (!in_array($trackId, $favorites)) {
            $favorites[] = $trackId;
            $user->update(['favorites' => $favorites]);
        }
        return response()->json(['favorites' => $favorites]);
    }

    public function removeFavorite(Request $request, $trackId)
    {
        $user = $request->user();
        $favorites = array_values(array_filter($user->favorites ?? [], fn($id) => $id != $trackId));
        $user->update(['favorites' => $favorites]);
        return response()->json(['favorites' => $favorites]);
    }

    public function addRecentlyPlayed(Request $request, $trackId)
    {
        $user = $request->user();
        $recent = array_values(array_filter($user->recently_played ?? [], fn($id) => $id != $trackId));
        array_unshift($recent, $trackId);
        $recent = array_slice($recent, 0, 50);
        $user->update(['recently_played' => $recent]);
        return response()->json(['recently_played' => $recent]);
    }
}
