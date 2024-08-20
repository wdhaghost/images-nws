<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;
    public static function boot()
    {
        parent::boot();

        static::creating(function ($image) {

            $image->url = Str::uuid()->toString(); 
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
