<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;

class ImageController extends Controller
{
     protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Delete an image.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage(Image $image)
    {
        $this->imageService->deleteImage($image);

        return response()->json(['message' => 'Image deleted']);
    }
    /**
     * Handle the image upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $modelClass = '\\App\\Models\\' . $request->model_type;
        if (!class_exists($modelClass)) return response()->json(['error' => 'Model not found'], 404);

        $model = $modelClass::findOrFail($request->model_id);

        $path = $request->file('image')->store('uploads/images', 'public');

        $image = new Image(['path' => $path]);
        $model->images()->save($image);

        return response()->json(['message' => 'Image uploaded', 'image' => $image]);
    }
    

    /**
     * Get images for a specific amigurumi pattern.
     *
     * @param  int  $patternId
     * @return \Illuminate\Http\JsonResponse
     */
    public function amigurumiPattenIndex($patternId)
    {
        $pattern = \App\Models\AmigurumiPattern::findOrFail($patternId);
        return response()->json(
            $pattern->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                    'url' => asset('storage/' . $image->path), 
                ];
            })
        );
    }
}
