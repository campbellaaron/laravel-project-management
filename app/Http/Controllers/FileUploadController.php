<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class FileUploadController extends Controller
{
    public function index()
    {
        $files = Storage::disk('public')->allFiles('uploads');

        return view('files.index', compact('files'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,svg,ai,psd,gif|max:10240' // Allows images & PDFs, max 10MB
        ]);

        // Determine upload path
        $folder = $request->input('folder', 'general'); // Default to "general" if no folder is specified
        $path = $request->file('file')->store("uploads/{$folder}", 'public');


        return response()->json([
            'location' => Storage::url($path),
            'name' => $request->file('file')->getClientOriginalName(),
            'type' => $request->file('file')->getMimeType()
        ]);
    }

    public function destroy(Request $request)
    {
        Storage::disk('public')->delete($request->file);
        return back()->with('success', 'File deleted successfully.');
    }

}
