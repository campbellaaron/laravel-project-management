<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ImageUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240' // Max 10MB
        ]);

        $path = $request->file('image')->store('uploads', 'public');

        return response()->json([
            'location' => Storage::url($path) // Return the image URL to TinyMCE
        ]);
    }
}

