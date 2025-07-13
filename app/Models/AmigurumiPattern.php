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


}
