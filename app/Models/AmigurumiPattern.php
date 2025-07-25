<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmigurumiPattern extends Model
{
    protected $fillable = ['title', 'image_path', 'yarn_description', 'tools_description'];

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


}
