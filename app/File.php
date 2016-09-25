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
    
    public function items()
    {
        return $this->belongsToMany(Item::class);
    }

    public function getFilepath()
    {
        if (!is_null($this->item)) {
            return "{$this->item->path}/{$this->filename}";
        }
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('filename', 'LIKE', "%{$term}%");
    }
}
