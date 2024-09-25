<?php

namespace App\Http\Controllers\Api\App;

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
}
