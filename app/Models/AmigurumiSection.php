<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmigurumiSection extends Model
{
   protected $fillable = ['amigurumi_pattern_id', 'title', 'order'];


    public function amigurumiRows()
    {
        return $this->hasMany(AmigurumiRow::class);
    }
    public function amigurumiPattern()
    {
        return $this->belongsTo(AmigurumiPattern::class);
    }
    
}
