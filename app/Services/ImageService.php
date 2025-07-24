<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageService
{
    public function deleteImage(Image $image)
    {
    
        // Töröld a fizikai fájlt (feltételezve, hogy az útvonal a 'path' mezőben van)
      
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        } else {
            Log::warning('File not found: ' . $image->path);
        }

        // Töröld az adatbázisból
        $image->delete();
    }
}
