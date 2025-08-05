<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AmigurumiPatternAssemblyStep extends Model
{
    use HasFactory;

    protected $fillable = ['amigurumi_pattern_id', 'text', 'order'];

    public function pattern()
    {
        return $this->belongsTo(AmigurumiPattern::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
        //return $this->morphMany(Image::class, 'imageable')->orderBy('order');
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
