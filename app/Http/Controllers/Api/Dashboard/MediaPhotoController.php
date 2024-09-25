<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MediaPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaPhotoController extends Controller
{
    // Display a listing of the media photos
    public function index()
    {
        $mediaPhotos = MediaPhoto::first();
        return response()->json($mediaPhotos, 200);
    }

    // Store a new media photo
    public function store(Request $request)
    {
        $request->validate([
            'header_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'percentage_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'goals_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);
        $mediaPhoto = MediaPhoto::first();
        if ($mediaPhoto) {
         return response()->json(['error' => 'Already Uploaded'], 404);
    }
        // Store the uploaded files
        $headerPhotoPath = $request->file('header_photo')->store('media-photos', 'public');
        $percentagePhotoPath = $request->file('percentage_photo')->store('media-photos', 'public');
        $goalsPhotoPath = $request->file('goals_photo')->store('media-photos', 'public');

        // Create the media photo record
        $mediaPhoto = MediaPhoto::create([
            'header_photo' => $headerPhotoPath,
            'percentage_photo' => $percentagePhotoPath,
            'goals_photo' => $goalsPhotoPath,
        ]);

        return response()->json($mediaPhoto, 201);
    }

    // Show the specified media photo
    public function show($id)
    {
        $mediaPhoto = MediaPhoto::findOrFail($id);
        return response()->json($mediaPhoto, 200);
    }

    // Update the specified media photo
    public function update(Request $request)
    {
        $mediaPhoto = MediaPhoto::first();

        $request->validate([
            'header_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'percentage_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'goals_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        // Update and store the photos if present in the request
        if ($request->hasFile('header_photo')) {
            Storage::disk('public')->delete($mediaPhoto->header_photo);
            $mediaPhoto->header_photo = $request->file('header_photo')->store('media-photos', 'public');
        }

        if ($request->hasFile('percentage_photo')) {
            Storage::disk('public')->delete($mediaPhoto->percentage_photo);
            $mediaPhoto->percentage_photo = $request->file('percentage_photo')->store('media-photos', 'public');
        }

        if ($request->hasFile('goals_photo')) {
            Storage::disk('public')->delete($mediaPhoto->goals_photo);
            $mediaPhoto->goals_photo = $request->file('goals_photo')->store('media-photos', 'public');
        }

        $mediaPhoto->save();

        return response()->json($mediaPhoto, 200);
    }

    // Remove the specified media photo
    public function destroy()
    {
        $mediaPhoto = MediaPhoto::first();

        // Delete the photos from storage
        Storage::disk('public')->delete([$mediaPhoto->header_photo, $mediaPhoto->percentage_photo, $mediaPhoto->goals_photo]);

        // Delete the record from the database
        $mediaPhoto->delete();

        return response()->json(['message' => 'Media photo deleted successfully'], 200);
    }
}
