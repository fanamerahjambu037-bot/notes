<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['title', 'category_id', 'type', 'content', 'file_url', 'file_path', 'file_name'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
