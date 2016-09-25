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
        return $this->belongsToMany(File::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeSearch($query, $term)
    {
        $terms = explode(':', $term);
        
        if (count($terms) === 2) {
            $term = $terms[1];
            $query = $query->whereHas('category', function($query) use ($terms) {
                $query->where('name', 'LIKE', "%{$terms[0]}%");
            });
        } else {
            $term = $terms[0];
        }
        
        return $query
            ->where(function ($query) use ($term) {
                return $query->whereHas('files', function ($query) use ($term) {
                    return $query->search($term);
                })
                    ->orWhere('title', 'LIKE', "%{$term}%");
            });
    }
}
