<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmigurumiRow extends Model
{
    protected $fillable = ['amigurumi_section_id', 'row_number', 'instructions'];

    public function section()
    {
        return $this->belongsTo(AmigurumiSection::class);
    }
}
