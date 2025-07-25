<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['path', 'order', 'is_main'];

    public function imageable()
    {
        return $this->morphTo();
    }
}

