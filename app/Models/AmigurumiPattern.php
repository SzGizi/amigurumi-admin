<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AmigurumiPattern extends Model
{
    protected $fillable = [
        'title', 
        'image_path', 
        'yarn_description', 
        'tools_description',
        'final_size',
        'difficulty',
        'introduction',
        'abbreviations',
    ];

    public function amigurumiSections()
    {
        return $this->hasMany(AmigurumiSection::class)->orderBy('order');
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function mainImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_main', true);
    }
    public function getMainImageIdAttribute()
    {
        return optional($this->mainImage)->id;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
      static::deleting(function ($pattern) {
            foreach ($pattern->images as $image) {
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
            $pattern->images()->delete();
        });
    }
    public function assemblySteps()
    {
        return $this->hasMany(AmigurumiPatternAssemblyStep::class)->orderBy('order');
    }



}
