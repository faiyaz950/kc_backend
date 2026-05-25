<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\User::orderBy('created_at', 'desc')->get());
    }

    public function show($id)
    {
        return response()->json(\App\Models\User::findOrFail($id));
    }

    public function destroy($id)
    {
        \App\Models\User::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
