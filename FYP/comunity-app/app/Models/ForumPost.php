<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'content',
        'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(ForumLike::class);
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class);
    }
}
