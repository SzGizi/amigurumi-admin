<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialLink extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'link', 'icon', 'order'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
