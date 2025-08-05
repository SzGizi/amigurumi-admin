<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return $this->morphMany(Image::class, 'model')->orderBy('order_column');
    }
}
