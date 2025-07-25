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

    public function setMainImage(Image $image)
    {
        // TODO: Törlés csak az aktuális pattern vagy services main képről

        $imageable = $image->imageable;

        // Töröljük a korábbi main képet
        $imageable->images()->update(['is_main' => false]);

        // Beállítjuk ezt main képnek
        $image->is_main = true;
        $image->save();
    }
}
