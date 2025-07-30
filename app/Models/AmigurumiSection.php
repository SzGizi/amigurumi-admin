<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AmigurumiSection extends Model
{
   protected $fillable = ['amigurumi_pattern_id', 'title', 'order'];


    public function amigurumiRows()
    {
        return $this->hasMany(AmigurumiRow::class)->orderBy('order');
    }
    public function amigurumiPattern()
    {
        return $this->belongsTo(AmigurumiPattern::class);
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    protected static function booted()
    {
       static::deleting(function ($section) {
            foreach ($section->images as $image) {
                // Ha a képek a storage/app/public mappában vannak
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                
                // Ha a képek a public mappában vannak
                elseif (File::exists(public_path($image->path))) {
                    File::delete(public_path($image->path));
                }
                
                // Alternatíva: ha a path már teljes útvonalat tartalmaz
                elseif (File::exists($image->path)) {
                    File::delete($image->path);
                }
            }
            
            // Adatbázis recordok törlése
            $section->images()->delete();
        });
    }
}
