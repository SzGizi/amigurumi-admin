<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log;

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
        //Log::info('Beérkezett  kérés', $request->all());
    

      $request->validate([
        'image' => 'required|image|max:2048',
        'model_type' => 'required|string',
        'model_id' => 'required|integer',
        'order' => 'nullable|integer',
        'is_main' => 'nullable|integer|between:0,1',
        'caption' => 'nullable|string',
        ]);

        
        
        $modelClass = '\\App\\Models\\' . $request->model_type;
        if (!class_exists($modelClass)) return response()->json(['error' => 'Model not found'], 404);

        $model = $modelClass::findOrFail($request->model_id);

        $path = $request->file('image')->store('uploads/images', 'public');
        

        $image = new Image(['is_main'=>$request->is_main, 'caption'=>$request->caption, 'order'=>$request->order, 'path' => $path]);

        $model->images()->save($image);

      

        if($this->imageService->setMainImage($image)){
            $this->imageService->setMainImage($image);
        }
       
        return response()->json([
            'message' => 'Image uploaded',
            'image' => [
                'id' => $image->id,
                'url' => asset('storage/' . $image->path), // <- Ezt a Vue használja
            ]
        ]);
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
        $images = $pattern->images->sortBy('order')->values();
        return response()->json(
            $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                    'order' => $image->order,
                    'is_main' => $image->is_main,
                    'caption' => $image->caption, 
                    
                    'url' => asset('storage/' . $image->path), 
                ];
            })
        );
    }
    public function amigurumiSectionIndex($sectionId)
    {
        $section = \App\Models\AmigurumiSection::findOrFail($sectionId);
        //Log::info('AmigurumiSectionIndex called for section ID: ' . $sectionId);
        //Log::info('Section images:', $section->images->toArray());
        $images = $section->images->sortBy('order')->values();
        return response()->json(
            $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                    'order' => $image->order,
                    'is_main' => $image->is_main,
                    'caption' => $image->caption, 
                    'url' => asset('storage/' . $image->path), 
                ];
            })
        );
    }

    
     public function amigurumiAssemblyStepIndex($assemblystepId)
    {
        //Log::info('Fetching images for AssemblyStep', ['id' => $assemblystepId]);

        $assemblyStep = \App\Models\AmigurumiPatternAssemblyStep::findOrFail($assemblystepId);
      
        $images = $assemblyStep->images->sortBy('order')->values();
        return response()->json(
            $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                    'order' => $image->order,
                    'is_main' => $image->is_main,
                    'caption' => $image->caption, 
                    'url' => asset('storage/' . $image->path), 
                ];
            })
        );
    }
    
    /**
     * Set an image as the main image for its model.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function setMain(Image $image)
    {
        $this->imageService->setMainImage($image);

        return response()->json(['status' => 'ok', 'message' => 'Main image set.']);
        
    }

    public function reorder(Request $request)
    {
        $images = $request->input('images');

        foreach ($images as $imgData) {
            \App\Models\Image::where('id', $imgData['id'])->update([
                'order' => $imgData['order'],
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Replace an existing image with a new one.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function replace(Request $request, Image $image)
    {
        
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        // Töröljük a régi fájlt
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        // Feltöltjük az újat
        $path = $request->file('image')->store('uploads/images', 'public');

        // Frissítjük az adatbázisban az elérési utat
        $image->path = $path;
        $image->save();

        return response()->json([
            'message' => 'Image replaced',
            'image' => [
                'id' => $image->id,
                'url' => asset('storage/' . $path),
            ]
        ]);
    }
    /**
     * Update the caption of an image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCaption(Request $request, Image $image)
{
    $request->validate([
        'caption' => 'nullable|string|max:255',
    ]);

    $image->caption = $request->caption;
    $image->save();

    return response()->json(['status' => 'ok', 'message' => 'Caption updated']);
}


}
