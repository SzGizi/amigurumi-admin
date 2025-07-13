<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmigurumiRow extends Model
{
    protected $fillable = ['amigurumi_section_id', 'row_number', 'instructions', 'stitch_number','comment', 'order'];

    public function amigurumiSection()
    {
        return $this->belongsTo(AmigurumiSection::class);
    }
}
