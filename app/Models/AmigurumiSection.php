<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmigurumiSection extends Model
{
   protected $fillable = ['amigurumi_pattern_id', 'title', 'order'];

    public function pattern()
    {
        return $this->belongsTo(AmigurumiPattern::class);
    }

    public function rows()
    {
        return $this->hasMany(AmigurumiRow::class);
    }
}
