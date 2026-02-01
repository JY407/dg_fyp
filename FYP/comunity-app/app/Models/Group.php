<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
