<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{   
    protected $fillable = [
        'dev',
        'inode',
        'filename',
        'size',
    ];
    
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
