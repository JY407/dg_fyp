<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model
{
    protected $fillable = ['user_id', 'type', 'message', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
