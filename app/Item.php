<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'title',
        'path',
    ];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
